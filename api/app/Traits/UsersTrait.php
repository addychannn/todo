<?php

namespace App\Traits;

use Session;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Profile;
use App\Models\Images;
use App\Models\Gender;
use App\Models\Address;
use App\Models\Address\AddressType;
use App\Models\Logs;

use App\Http\Resources\UserResource;

trait UsersTrait {
    use LikeToggleTrait, ImageTrait;

    protected function signout(Request $request) {
        if ((bool) env("APP_AUTH_TOKEN", false)) {
            $request
                ->user()
                ->tokens()
                ->delete();
            // auth()->logout();
        } else {
            Session::flush();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
    }

    public function searchUserQuery($search = "", $column = "name") {
        $searchKeys = preg_split("/\s+/", $search ?? "", -1, PREG_SPLIT_NO_EMPTY);
        $user = User::where(function ($query) use ($searchKeys, $column) {
            foreach ($searchKeys as $key) {
                $query
                    ->when($column != "name", function ($qq) use ($key, $column) {
                        $qq->orWhere($column, self::LikeToggle(), "%" . $key . "%");
                    })
                    ->when($column == "name", function ($qq) use ($key, $column) {
                        $qq->orWhere(function ($q) use ($key) {
                            $q->whereRelation(
                                "profile",
                                "first_name",
                                self::LikeToggle(),
                                "%" . $key . "%"
                            );
                        })
                            ->orWhere(function ($q) use ($key) {
                                $q->whereRelation(
                                    "profile",
                                    "middle_name",
                                    self::LikeToggle(),
                                    "%" . $key . "%"
                                );
                            })
                            ->orWhere(function ($q) use ($key) {
                                $q->whereRelation(
                                    "profile",
                                    "last_name",
                                    self::LikeToggle(),
                                    "%" . $key . "%"
                                );
                            });
                    });
            }
        });
        return $user;
    }

    public function searchUser(
        $search,
        $limit = 10,
        $offset = 0,
        $orderBy = "username",
        $order = "asc",
        $column = "name"
    ) {
        $user = $this->searchUserQuery($search, $column)
            ->when($orderBy == "name", function ($query) use ($order) {
                $query
                    ->orderBy(
                        Profile::select("first_name")->whereColumn("profiles.user_id", "users.id"),
                        $order
                    )
                    ->orderBy(
                        Profile::select("middle_name")->whereColumn("profiles.user_id", "users.id"),
                        $order
                    )
                    ->orderBy(
                        Profile::select("last_name")->whereColumn("profiles.user_id", "users.id"),
                        $order
                    );
            })
            ->when($orderBy != "name", function ($query) use ($orderBy, $order) {
                $query->orderBy($orderBy, $order);
            })
            ->offset($offset)
            ->limit($limit)
            ->with("profile")
            ->with("roles")
            ->with("profile.gender")
            ->with("permissions")
            ->with("profile.addresses")
            ->with("profile.addresses.barangay")
            ->with("profile.addresses.type")
            ->with("profile.image")
            ->with("profile.images");
        return $user;
    }

    public function searchUserCount($search, $column = "name") {
        $user = $this->searchUserQuery($search, $column)->selectRaw("count(*) as count");
        return $user;
    }

    public function searchUserActivity($limit = 10, $offset = 0, $orderBy = "id", $order = "asc") {
        return Logs::when($orderBy == "date", function ($query) use ($orderBy, $order) {
            $query->orderBy("created_at", $order);
        })
            ->offset($offset)
            ->limit($limit);
    }

    public function searchUserActivityCount() {
        return Logs::selectRaw("count(*) as count");
    }

    public function uploadAvatar(Request $request, $user_id, $module_name) {
        $user = gettype($user_id) == "string" ? User::byHashOrFail($user_id) : $user_id;
        $image = $this->uploadImage($request, "Avatars" . $user->hash);
        if ($request->input("chunk") == 0) {
            $this->logInfo(
                "Image upload initialized",
                $module_name,
                $this->ACTION_UPDATE,
                null,
                $image ?? null
            );
        }
        if (!$image["uid"]) {
            $profile = $user
                ->profile()
                ->updateOrCreate(["user_id" => $user->id], ["image_id" => $image->id]);
            $profile->images()->attach($image->id);

            $this->logInfo(
                "User profile updated (IMAGE)",
                $module_name,
                $this->ACTION_UPDATE,
                $profile->getOriginal(),
                $profile->getChanges()
            );
            $user->refresh();
            return response([
                "data" => new UserResource($user),
                "message" => "Avatar updated successfully",
            ]);
        }
        return response($image);
    }

    public function updateAvatar(Request $request, User $user, $module_name) {
        $avatars = $user->profile?->images->pluck("id");
        $image_id = Images::hashToId($request->input("image"));
        $is_valid = $avatars->contains($image_id);
        if ($is_valid) {
            $profile = $user
                ->profile()
                ->updateOrCreate(["user_id" => $user->id], ["image_id" => $image_id]);
            $user->refresh();
            $this->logInfo(
                "User profile updated (IMAGE)",
                $module_name,
                $this->ACTION_UPDATE,
                $profile->getOriginal(),
                $profile->getChanges()
            );
            return [
                "data" => new UserResource($user),
                "message" => "Profile picture updated successfully!",
            ];
        }

        $this->logWarning(
            "Failed to update profile image. Image not found!",
            $module_name,
            $this->ACTION_UPDATE
        );
        return response(["message" => "Failed to update profile image! Image not found!"], 422);
    }

    public function changeName(Request $request, User $user, $module_name) {
        $request->validate([
            "first_name" => "required",
            "middle_name" => "",
            "last_name" => "required",
            "suffix" => "",
            "nickname" => "",
        ]);

        $profile = $user->profile()->updateOrCreate(
            [
                "user_id" => $user->id,
            ],
            [
                "first_name" => $request->input("first_name"),
                "middle_name" => $request->input("middle_name"),
                "last_name" => $request->input("last_name"),
                "suffix" => $request->input("suffix"),
                "nickname" => $request->input("nickname"),
            ]
        );
        $this->logInfo(
            "User profile updated (NAME)",
            $module_name,
            $this->ACTION_UPDATE,
            $profile->getOriginal(),
            $profile->getChanges()
        );
        $user->refresh();
        return response([
            "data" => new UserResource($user),
            "message" => "Profile name updated successfully",
        ]);
    }

    public function changeBirthdate(Request $request, User $user, $module_name) {
        $request->validate([
            "birthdate" => "required|date|before:tomorrow",
        ], [
            "birthdate.before" => "Unfortunately, time travelers are not permitted."
        ]);
        $profile = $user->profile()->updateOrCreate(
            ["user_id" => $user->id],
            [
                "birthdate" => $request->input("birthdate"),
            ]
        );
        $this->logInfo(
            "User profile updated (BIRTHDATE)",
            $module_name,
            $this->ACTION_UPDATE,
            $profile->getOriginal(),
            $profile->getChanges()
        );
        $user->refresh();
        return response([
            "data" => new UserResource($user),
            "message" => "Birthdate updated successfully",
        ]);
    }

    public function changeGender(Request $request, User $user, $module_name) {
        $request->merge([
            "gender" => Gender::hashToId($request->input("gender")),
        ]);

        $request->validate([
            "gender" => "required|exists:genders,id",
        ]);

        $profile = $user->profile()->updateOrCreate(
            [
                "user_id" => $user->id,
            ],
            [
                "gender_id" => $request->input("gender"),
            ]
        );

        $this->logInfo(
            "User profile updated (GENDER)",
            $module_name,
            $this->ACTION_UPDATE,
            $profile->getOriginal(),
            $profile->getChanges()
        );
        return response([
            "data" => new UserResource($user),
            "message" => "Gender updated successfully",
        ]);
    }

    public function addAddress(
        Request $request,
        User $user,
        $module_name,
        Address $address = null
    ) {
        $request->merge([
            "type" => !!$request->input("type", null)
                ? AddressType::hashToId($request->input("type", null))
                : null,
        ]);
        $request->validate([
            "barangay" => "required|string|exists:barangays,code",
            "location" => "required|string",
            "type" => "nullable|exists:address_types,id",
            "zipCode" => "required|digits:4",
            "isMain" => "boolean",
        ]);

        $profile = $user->profile()->firstOrCreate();
        $addresses = $profile?->addresses ?? [];
        $addressExists = $profile
            ?->addresses()
            ->where("location", $request->input("location"))
            ->where("barangayCode", $request->input("barangay"))
            ->where("zipCode", $request->input("zipCode"))
            ->where("type_id", $request->input("type", null))
            ->first();

        if ($addressExists && !$address) {
            $this->logNotice(
                "User profile failed to add address (DUPLICATE ADDRESS)",
                $module_name,
                $this->ACTION_UPDATE,
                $profile->getOriginal(),
                $request->all()
            );
            return response(
                [
                    "message" => "Address already exists!",
                ],
                422
            );
        }

        $isMain = count($addresses) <= 0;

        $address_data = [
            "type_id" => $request->input("type", null),
            "location" => $request->input("location"),
            "barangayCode" => $request->input("barangay"),
            "zipCode" => $request->input("zipCode"),
            "isMain" => $request->input("isMain"),
        ];

        if (count($addresses) <= 0 || ($address?->isMain && $request->input("isMain") == false)) {
            $address_data["isMain"] = true;
        } elseif (!$address?->isMain && $request->input("isMain")) {
            Address::whereIn("id", collect($addresses)->pluck("id"))->update([
                "isMain" => false,
            ]);
        }
        $created = false;
        if (!!$address) {
            if (collect($addresses)->contains("id", $address->id)) {
                $created = true;
                $address->update($address_data);
            } else {
                $this->logNotice(
                    "User profile failed to update address (UNKNOWN ADDRESS)",
                    $module_name,
                    $this->ACTION_UPDATE,
                    $profile->getOriginal(),
                    $request->all()
                );
                return response("Unknown address!", 404);
            }
        } else {
            $profile->addresses()->create($address_data);
        }

        $this->logInfo(
            "User profile address " . $created ? "added" : "updated" . " (ADDRESS)",
            $module_name,
            $this->ACTION_UPDATE,
            $profile->getOriginal(),
            $profile->getChanges()
        );
        $user->refresh();
        return response([
            "data" => new UserResource($user),
            "message" => "Address updated successfully",
        ]);
    }

    public function changeMainAddress(User $user, Address $address, $module_name) {
        $profile = $user->profile;
        $addresses = $profile?->addresses ?? [];
        if (collect($addresses)->contains("id", $address->id)) {
            if (!$address->isMain) {
                Address::whereIn("id", collect($addresses)->pluck("id"))->update([
                    "isMain" => false,
                ]);
                $address->update(["isMain" => true]);
                $user->refresh();
            }
        }

        $this->logInfo(
            "User profile updated (MAIN ADDRESS)",
            $module_name,
            $this->ACTION_UPDATE,
            $profile->getOriginal(),
            $profile->getChanges()
        );
        return response([
            "data" => new UserResource($user),
            "message" => "Address set as main successfully",
        ]);
    }

    public function removeAddress(User $user, Address $address, $module_name) {
        $profile = $user->profile;
        $addresses = $profile?->addresses ?? [];
        if (collect($addresses)->contains("id", $address->id)) {
            if (!$address->isMain) {
                $address->delete();
                $user->refresh();
                $this->logInfo(
                    "User profile updated (ADDRESS DELETED)",
                    $module_name,
                    $this->ACTION_UPDATE,
                    $profile->getOriginal(),
                    $profile->getChanges()
                );
                return response([
                    "data" => new UserResource($user),
                    "message" => "Address deleted successfully",
                ]);
            } else {
                $this->logNotice(
                    "User profile failed to delete address (CANNOT DELETE MAIN ADDRESS!)",
                    $module_name,
                    $this->ACTION_UPDATE,
                    $profile->getOriginal(),
                    $request->all()
                );
                return response(["message" => "Unable to delete main address!"], 404);
            }
        }
        $this->logNotice(
            "User profile failed to delete address (CANNOT DELETE UNKNOWN ADDRESS!)",
            $module_name,
            $this->ACTION_UPDATE,
            $profile->getOriginal(),
            $request->all()
        );
        return response(["message" => "Unknown address!"], 404);
    }

    public function changeUsername(Request $request, User $user, $module_name) {
        $request->merge([
            "username" => strtolower($request->input("username")),
        ]);
        $request->validate([
            "username" => "required|string|unique:users,username," . $user->id,
        ]);

        if ($request->input("username") != $user->username) {
            $user->update([
                "username" => $request->input("username"),
            ]);
            if($user->id != auth()->user()->id){
                $user->sendAdminAccountChangedNotification();
            }else{
                $user->sendAccountChangedNotification();
            }
        }

        $this->logInfo(
            "User account updated (USERNAME)",
            $module_name,
            $this->ACTION_UPDATE,
            $user->getOriginal(),
            $user->getChanges()
        );

        if ($user->id == auth()->id()) {
            $this->signout($request);
        }

        return response([
            "data" => new UserResource($user),
            "message" => "Username updated successfully",
        ]);
    }

    public function changeEmail(Request $request, User $user, $module_name) {
        $request->merge([
            "email" => strtolower($request->input("email")),
        ]);
        $request->validate([
            "email" => "required|email|unique:users,email," . $user->id,
        ]);

        if ($request->input("email") != $user->email) {
            $user->update([
                "email" => $request->input("email"),
                "email_verified_at" => null,
            ]);
            if($user->id != auth()->user()->id){
                $user->sendAdminAccountChangedNotification();
            }else{
                $user->sendAccountChangedNotification();
            }
            $user->sendEmailVerification();
        }

        $this->logInfo(
            "User account updated (EMAIL)",
            $module_name,
            $this->ACTION_UPDATE,
            $user->getOriginal(),
            $user->getChanges()
        );

        if ($user->id == auth()->id()) {
            $this->signout($request);
        }

        return response([
            "data" => new UserResource($user),
            "message" => "Email updated successfully",
        ]);
    }

    public function sendVerificationLink($email, $module_name) {
        $user = User::where("email", $email)->first();

        if (!$user) {
            $this->logNotice(
                "User email verification failed to send (USER NOT FOUND)",
                $module_name,
                $this->ACTION_UPDATE,
                null,
                $email
            );
            return response(["message" => "User not found!"], 422);
        }

        if ($user->emai_verified_at == null) {
            $user->sendEmailVerification();

            $this->logInfo(
                "Email verification link send.",
                $module_name,
                $this->ACTION_UPDATE,
                null,
                $email
            );
        }

        return response(["message" => "Verification link sent!"]);
    }
}

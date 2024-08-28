<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\Profile;
use App\Models\AccountStatusHistory;
use App\Models\Images;
use App\Models\Gender;
use App\Models\Address;
use App\Models\Address\AddressType;

use App\Traits\UsersTrait;
use App\Traits\PermissionsTrait;
use App\Traits\LikeToggleTrait;

use App\Http\Resources\UserResource;
use App\Http\Resources\RolesResource;
use App\Http\Resources\PermissionsResource;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsersController extends Controller {
    use UsersTrait, PermissionsTrait, LikeToggleTrait;

    public function __construct() {
        $permissions = [
            "users_add",
            "users_list",
            "users_edit-profile",
            "users_edit-account",
            "users_edit-permission",
            "users_change-status",
            "users_give-direct-permissions",
        ];

        $this->middleware("permission:" . implode("|", $permissions), [
            "only" => ["userSearch", "getUser", "getUserStatusHistory", "getRoles"],
        ]);

        // $this->middleware("permission:users_add|users_edit-permission", ["only" => ["getRoles"]]);
        $this->middleware("permission:users_add", ["only" => ["addUser"]]);
        $this->middleware("permission:users_edit-profile", [
            "only" => [
                "updateName",
                "changeAvatar",
                "updateBirthdate",
                "updateGender",
                "saveAvatar",
                "setAddress",
                "setMainAddress",
                "deleteAddress",
                "setMainAddress",
                "deleteAddress",
            ],
        ]);
        $this->middleware("permission:users_edit-account", [
            "only" => ["updateUsername", "updateEmail", "updatePassword"],
        ]);
        $this->middleware("permission:users_change-status", [
            "only" => ["toggleUserActive", "verifyEmail"],
        ]);
        $this->middleware("permission:users_edit-permission|users_give-direct-permissions", [
            "only" => ["setPermissions", "searchPermissions"],
        ]);
    }

    public function userSearch(Request $request) {
        $search = $request->input("search", []);
        $limit = $request->input("limit", 10);
        $offset = $request->input("offset", 0);
        $orderBy = $request->input("orderBy", "username");
        $order = $request->input("order", "asc");
        $roles = $request->input("roles", null);
        $column = "name";

        $auth = auth()->user();
        $isSuperAdmin = $auth->hasRole("Admin");
        $roles = collect($roles)
            ->map(function ($item) {
                return Role::hashToId($item["value"]);
            })
            ->toArray();

        $user = $this->searchUser($search["name"] ?? "", $limit, $offset, $orderBy, $order, $column)
            ->when(array_key_exists("username", $search), function ($q) use ($search) {
                $q->where("username", self::LikeToggle(), "%" . $search["username"] . "%");
            })
            ->when(array_key_exists("email", $search), function ($q) use ($search) {
                $q->where("email", self::LikeToggle(), "%" . $search["email"] . "%");
            })
            ->when(count($roles) > 0, function ($q) use ($roles) {
                $q->whereHas("roles", function ($qq) use ($roles) {
                    $qq->whereIn("role_id", $roles);
                }); //, "=", count($roles)
            })
            ->where("id", "!=", $auth->id)
            ->when(!$isSuperAdmin, function ($query) use ($auth) {
                // return $query->whereRelation("roles", "name", "!=", "Admin")->orDoesntHave("roles");
                $query->whereDoesntHave("roles", function ($q) {
                    $q->where("name", "Admin");
                });
            })
            ->get();

        $count = $this->searchUserCount($search["name"] ?? "", $column)
            ->when(array_key_exists("username", $search), function ($q) use ($search) {
                $q->where("username", self::LikeToggle(), "%{$search["username"]}%");
            })
            ->when(array_key_exists("email", $search), function ($q) use ($search) {
                $q->where("email", self::LikeToggle(), "%{$search["email"]}%");
            })
            ->when(count($roles) > 0, function ($q) use ($roles) {
                $q->whereHas("roles", function ($qq) use ($roles) {
                    $qq->whereIn("role_id", $roles);
                }); //, "=", count($roles)
            })
            ->where("id", "!=", $auth->id)
            ->when(!$isSuperAdmin, function ($query) use ($auth) {
                // return $query->whereRelation("roles", "name", "!=", "Admin")->orDoesntHave("roles");
                $query->whereDoesntHave("roles", function ($q) {
                    $q->where("name", "Admin");
                });
            })
            ->first()->count;

        return response([
            "count" => $count,
            "data" => UserResource::collection($user),
        ]);
    }

    public function getUser(User $user) {
        $auth = auth()->user();
        $isSuperAdmin = $auth->hasRole("Admin");

        if (!$isSuperAdmin && ($user->id == $auth->id || $user->hasRole("Admin"))) {
            return response(
                [
                    "message" => "You are not allowed to access this user",
                ],
                422
            );
        }

        return response([
            "data" => new UserResource($user),
        ]);
    }

    public function getUserStatusHistory(User $user) {
        $history = $user
            ->getStatusHistory()
            ->orderBy("created_at", "desc")
            ->limit(10)
            ->get();
        return response([
            "data" => $history,
        ]);
    }

    public function getRoles(Request $request) {
        $includePermissions = $request->input("p", false);

        $auth = auth()->user();
        $isSuperAdmin = $auth->hasRole("Admin");

        $roles = Role::when(!$isSuperAdmin, function ($query) {
            return $query->where("name", "<>", "Admin");
        })
            ->with("permissions")
            ->get();

        if ($includePermissions) {
            $roles = RolesResource::collection($roles);
            return response([
                "roles" => $roles,
                "permissions" => PermissionsResource::collection(
                    Permission::orderBy("name", "ASC")->get()
                ),
            ]);
        }
        return response([
            "data" => RolesResource::collection($roles),
        ]);
    }

    public function searchPermissions(Request $request) {
        $search = $request->input("search");
        $limit = $request->input("limit", 20);
        $offset = $request->input("offset", 0);
        $orderBy = $request->input("orderBy", "name");
        $order = $request->input("order", "asc");
        $column = $request->input("column", "name");

        $permissions = $this->searchPermission($search, $limit, $offset, $orderBy, $order)
            ->where("name", "!=", "none")
            ->select("id", "name")
            ->get();
        $count = $this->searchPermissionCount($search)
            ->where("name", "!=", "none")
            ->first()->count;

        return response([
            "data" => $permissions->map(function ($item) {
                return [
                    "id" => $item->hash,
                    "name" => $item->name,
                ];
            }),
            "count" => $count,
        ]);
    }

    public function addUser(Request $request) {
        $request->merge([
            "roles" => collect($request->input("roles"))
                ->map(function ($item) {
                    return Role::hashToId($item);
                })
                ->toArray(),
            'gender' => Gender::hashToId($request->input('gender')),
            'username' => strtolower($request->input('username'))
        ]);
        $request->validate([
            "username" => "required|max:75|unique:users,username",
            "password" => "required|min:8",
            "email" => "nullable|email|max:255|unique:users,email",
            "roles" => "nullable|array",
            "roles.*" => "exists:roles,id",

            // Profile
            'first_name' => 'required|max:50',
            'middle_name' => 'nullable|max:50',
            'last_name' => 'required|max:50',
            'gender' => 'required|exists:genders,id',
            'birthdate' => 'nullable|date|before:tomorrow',
        ]);

        $roles = Role::where("name", "User")->get();
        if (count($request->input("roles")) > 0) {
            $roles = Role::whereIn("id", $request->input("roles"))->get();
        }

        $auth = auth()->user();
        $adminRole = $roles->first(function ($item) {
            return $item->name == "Admin";
        });
        if (!!$adminRole && !$auth->hasRole("Admin")) {
            $this->logError(
                'An unauthorized attempted to create a "Super Admin" Account!',
                "User Management",
                $this->ACTION_CREATE,
                null,
                [$request->all(), $roles]
            );
            return response(
                [
                    "message" => "Unauthorized action detected!",
                ],
                403
            );
        }

        $user = User::create([
            "username" => $request->input('username'),
            "password" => Hash::make($request->input('password')),
            "email" => $request->input('email'),
        ]);

        $user->markEmailAsVerified(); // Mark email as verified regardless of if email exists since account is created by admin.

        $user->syncRoles($roles);
        $this->createProfile($request, $user);

        $result = new UserResource($user);

        $this->logInfo("New user  created", "User Management", $this->ACTION_CREATE, null, $result);

        return response(
            [
                "message" => "User created successfully",
                "data" => $result,
            ],
            201
        );
    }

    private function createProfile(Request $request, User $user){
        $user->profile()->create([
            'first_name' => $request->input('first_name'),
            'middle_name' => $request->input('middle_name'),
            'last_name' => $request->input('last_name'),
            'gender_id' => $request->input('gender'),
            'birthdate' => $request->input('birthdate'),
        ]);
        $user->refresh();
    }

    public function updateName(Request $request, User $user) {
        return $this->changeName($request, $user, "User Management");
    }

    public function updateUsername(Request $request, User $user) {
        return $this->changeUsername($request, $user, "User Management");
    }

    public function updateEmail(Request $request, User $user) {
        return $this->changeEmail($request, $user, "User Management");
    }

    public function saveAvatar(Request $request, $id) {
        return $this->uploadAvatar($request, $id, "User Management");
    }

    public function changeAvatar(Request $request, User $user) {
        return $this->updateAvatar($request, $user, "User Management");
    }

    public function updatePassword(Request $request, User $user) {
        $request->validate([
            "password" => "required|string|min:8",
            "notify" => "boolean",
        ]);

        $user->update(["password" => Hash::make($request->input("password"))]);

        if ($request->input("notify") == "true" && $user->email != null) {
            $user->sendAdminPasswordChangedNotification($request->input("password"));
        }

        $result = new UserResource($user);
        $this->logInfo(
            "Usere Account updated (PASSWORD)",
            "User Management",
            $this->ACTION_UPDATE,
            null,
            $result
        );
        //logout user
        $user->tokens()->delete();
        $user->sessions()->delete();

        return response(
            [
                "data" => $result,
                "message" => "Password updated successfully",
            ],
            200
        );
    }

    public function updateBirthdate(Request $request, User $user) {
        return $this->changeBirthdate($request, $user, "User Management");
    }

    public function updateGender(Request $request, User $user) {
        return $this->changeGender($request, $user, "User Management");
    }

    public function verifyEmail(Request $request, User $user) {
        if ($user && !$user->email_verified_at) {
            $user->update([
                "email_verified_at" => now(),
            ]);

            $this->logInfo(
                "Email verification successfull (" . $user->email ?? $user->username . ")",
                "User Management",
                $this->ACTION_UPDATE
            );
        }
        return response([
            "data" => new UserResource($user),
            "message" => "Email verified successfully",
        ]);
    }

    public function toggleUserActive(Request $request, User $user) {
        $disabled = false;
        if ($user->disabled_at) {
            $user->disabled_at = null;
        } else {
            $user->disabled_at = now();
            $disabled = true;
        }
        $user->save();
        $name = $request->user()->username;
        if ($request->user()->profile != null) {
            $name =
                $request->user()->profile->first_name . " " . $request->user()->profile->last_name;
        }
        $status = $disabled ? "Disabled" : "Enabled";
        $status .= " by";
        if (trim($name) != "") {
            $status .= " " . $name;
        }
        if ($user->email != null) {
            $status .= " (" . $request->user()->email . ")";
        } else {
            $status .= " (" . $request->user()->username . ")";
        }

        $history = AccountStatusHistory::create([
            "user_id" => $user->id,
            "status" => $status,
            "reason" => $request->input("remarks"),
        ]);

        $this->logInfo(
            "User status updated",
            "User Management",
            $this->ACTION_UPDATE,
            $user->getOriginal(),
            [$user->getChanges(), $history]
        );

        return response([
            "data" => new UserResource($user),
            "message" => "User status updated successfully",
        ]);
    }

    public function setPermissions(Request $request, User $user) {
        $_user = auth()->user();
        $_roles = [];
        $_permissions = [];
        $_validation = [];
        if ($_user->can("users_edit-permission")) {
            $_roles = collect($request->input("roles"))
                ->map(function ($item) {
                    return Role::hashToId($item);
                })
                ->toArray();
        }

        if ($_user->can("users_give-direct-permissions")) {
            $_permissions = collect($request->input("permissions"))
                ->map(function ($item) {
                    return Permission::hashToId($item);
                })
                ->toArray();
        }
        $request->merge([
            "roles" => $_roles,
            "permissions" => $_permissions,
        ]);

        $request->validate([
            "roles" => "array",
            "roles.*" => "exists:roles,id",

            "permissions" => "array",
            "permissions.*" => "exists:permissions,id",
        ]);

        $old = json_decode(json_encode(new UserResource($user)));
        if ($_user->can("users_give-direct-permissions")) {
            $user->syncPermissions($request->input("permissions"));
        }
        if ($_user->can("users_edit-permission")) {
            $user->syncRoles($request->input("roles"));
        }
        if (
            $_user->can("users_give-direct-permissions") &&
            count($request->input("roles")) <= 0 &&
            ($_user->can("users_edit-permission") && count($request->input("permissions")) <= 0)
        ) {
            $user->syncPermissions("none");
        }

        $result = new UserResource($user);
        $this->logInfo(
            "User account updated",
            "User Management",
            $this->ACTION_UPDATE,
            $old,
            $result
        );
        return response(
            [
                "message" => "Permissions updated successfully",
                "data" => $result,
            ],
            200
        );
    }

    public function setAddress(Request $request, User $user, Address $address = null) {
        return $this->addAddress($request, $user, "User Management", $address);
    }

    public function setMainAddress(User $user, Address $address) {
        return $this->changeMainAddress($user, $address, "User Management");
    }

    public function deleteAddress(User $user, Address $address) {
        return $this->removeAddress($user, $address, "User Management");
    }
}

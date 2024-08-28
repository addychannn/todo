<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Traits\UsersTrait;

use App\Models\User;
use App\Models\Profile;
use App\Models\Gender;
use App\Models\AccountStatusHistory;

use App\Http\Resources\ProfilesResource;
use App\Http\Resources\PermissionsResource;
use App\Http\Resources\PermissionNamesResource;
use App\Http\Resources\UserResource;

class AuthenticationController extends Controller {
    use UsersTrait;

    protected $maxLoginAttempts = 5;
    protected $lockoutTime = 300;

    public function login(Request $request) {
        $request->validate([
            "email" => "required|string",
            "password" => "required|string",
        ]);

        $field = filter_var($request->input("email"), FILTER_VALIDATE_EMAIL) ? "email" : "username";
        $user = User::where($field, $request->input("email"))->first();
        if (
            !Auth::attempt([
                $field => $request->input("email"),
                "password" => $request->input("password"),
            ])
        ) {
            if ($user && $user->disabled_at == null) {
                $user->fails++;
                if ($user->fails >= $this->maxLoginAttempts) {
                    $user->disabled_at = now();

                    AccountStatusHistory::create([
                        "user_id" => $user->id,
                        "status" => "Account Disabled.",
                        "reason" => "Multiple failed login attempts",
                    ]);
                }
                $user->save();
            }
            $this->logWarning("Failed Login Attempt", "Authentication", $this->ACTION_LOGIN, null, [
                "username/email" => $request->input("email"),
            ]);

            return response()->json(
                [
                    "message" => "Username or password is incorrect",
                ],
                401
            );
        }

        if ($user) {
            if ($user->disabled_at) {
                $this->logWarning(
                    "A disabled account was used to log in by the user.",
                    "Authentication",
                    $this->ACTION_LOGIN,
                    null,
                    $user
                );

                return response()->json(
                    [
                        "disabled" => true,
                        "message" =>
                            "Your account has been disabled, please contact your system administrator",
                    ],
                    401
                );
            } else {
                $user->fails = 0;
                $user->save();
                $user->clearSessions();
                session(["team_id" => $user->office_id]);
            }
        }
        $token = "";
        if ((bool) env("APP_AUTH_TOKEN", false)) {
            $token = $user->createToken($request->input("email"))->accessToken;
        } else {
            $request->session()->regenerate();
        }

        $response = array_merge(
            [
                "email" => $user->email,
                "username" => $user->username,
                "disabled" => $user->disabled_at ? true : false,
                "verified" => $user->email_verified_at,
                "permissions" => PermissionNamesResource::collection($user->getAllPermissions()),
                "roles" => $user->getRoleNames(),
                "profile" => $user->profile ? new ProfilesResource($user->profile) : null,
            ],
            env("APP_AUTH_TOKEN", false) ? ["access_token" => $token, "token_type" => "Bearer"] : []
        );

        $this->logNotice(
            "User has successfully logged in.",
            "Authentication",
            $this->ACTION_LOGIN,
            null,
            $response
        );
        return response($response);
    }

    public function register(Request $request, $user = null){
        if(!!$user){
            return $this->uploadAvatar($request, $user, "Authentication: Account Registration");
        }
        return $this->register_old($request);
    }

    public function register_old(Request $request) {
        $request->merge([
            'gender' => Gender::hashToId($request->input('gender')),
            'username' => strtolower($request->input('username')),
        ]);
        $request->validate([
            "username" => "required|string|unique:users,username",
            "email" => "required|string|email|unique:users,email",
            "password" => "required|string|min:8",
            "password_confirmation" => "required|string|min:8|same:password",

            // Profile
            'first_name' => 'required|max:50',
            'middle_name' => 'nullable|max:50',
            'last_name' => 'required|max:50',
            'gender' => 'required|exists:genders,id',
            'birthdate' => 'required|date|before:tomorrow',
        ], [
            "birthdate.before" => "Unfortunately, time travelers are not permitted."
        ]);

        $user = User::create([
            "username" => $request->input("username"),
            "email" => $request->input("email"),
            "password" => Hash::make($request->input("password")),
        ]);

        $user->assignRole("User");

        $user->sendEmailVerification();

        $this->setProfile($request, $user);

        $this->logNotice(
            "A new user has been registered.",
            "Authentication",
            $this->ACTION_CREATE,
            null,
            $user
        );
        return response()->json([
            'data' => [
                'id' => $user->hash,
            ],
            "message" => "User created successfully",
        ]);
    }

    private function setProfile(Request $request, User $user){
        $user->profile()->create([
            'first_name' => $request->input('first_name'),
            'middle_name' => $request->input('middle_name'),
            'last_name' => $request->input('last_name'),
            'gender_id' => $request->input('gender'),
            'birthdate' => $request->input('birthdate'),
        ]);

        $user->refresh();
    }

    public function logout(Request $request) {
        $this->logNotice("User signed out.", "Authentication", $this->ACTION_LOGOUT);
        if ((bool) env("APP_AUTH_TOKEN", false)) {
            $request
                ->user()
                ->tokens()
                ->delete();
            // auth()->logout();
        } else {
            $user = auth()->user();
            $user->unsetRelation("roles", "permissions");
            Session::flush();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $user->clearSessions();
        }
        return response()->json([
            "message" => "Successfully logged out",
        ]);
    }

    public function getPermissions(Request $request) {
        $user = auth()->user();
        return response([
            "data" => new UserResource($user),
        ]);
    }
}

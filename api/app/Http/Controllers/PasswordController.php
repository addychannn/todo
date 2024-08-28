<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Rules\MatchOldPassword;
use App\Models\User;

class PasswordController extends Controller
{
    public function forgot_password(Request $request)
    {
        $validator = $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );
        
        $this->logInfo('User forgot password. Recovery process started for ('.$request->input('email').') ',
            'Password Management',$this->ACTION_SEND_EMAIL, null, [
                'email' => $request->input('email'),
                'status' => $this->getPasswordStatus($status),
            ]);

        switch ($status) {
            case Password::INVALID_USER:
            case Password::RESET_LINK_SENT:
            default: 
                return response()->json(["message" => "A password reset email has been sent IF such an account exists"]);
                break;
        }

    }

    public function change_password(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
        //$status = Password::PASSWORD_RESET;
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();
                $user->tokens()->delete();
                event(new PasswordReset($user));
                $user->sendPasswordChangedNotification();
            }
        );

        $this->logNotice('User attempts to reset password', 'Password Management', $this->ACTION_UPDATE, null, [
            'token' => $request->input('token'),
            'email' => $request->input('email'),
            // 'password' => $request->input('password'),
            'status' => $this->getPasswordStatus($status),
        ]);
        switch ($status) {
            case Password::RESET_THROTTLED:
                $this->logWarning('Password reset failed! (Blocked Request!)', 'Password Management', $this->ACTION_UPDATE, null, [
                    'token' => $request->input('token'),
                    'email' => $request->input('email'),
                    // 'password' => $request->input('password'),
                    'status' => $this->getPasswordStatus($status),
                ]);
                break;
            case Password::INVALID_USER:
                $this->logWarning('Password reset failed! (Invalid User!)', 'Password Management', $this->ACTION_UPDATE, null, [
                    'token' => $request->input('token'),
                    'email' => $request->input('email'),
                    // 'password' => $request->input('password'),
                    'status' => $this->getPasswordStatus($status),
                ]);
                return response(["message" => "Invalid request"], 422);
                break;
            case Password::INVALID_TOKEN:
                $this->logWarning('Password reset failed! (Invalid Reset Token!)', 'Password Management', $this->ACTION_UPDATE, null, [
                    'token' => $request->input('token'),
                    'email' => $request->input('email'),
                    // 'password' => $request->input('password'),
                    'status' => $this->getPasswordStatus($status),
                ]);
                return response(["message" => "Invalid Token"], 422);
                break;
            default:
            $this->logNotice('Password reset successful!', 'Password Management', $this->ACTION_UPDATE, null, [
                'token' => $request->input('token'),
                'email' => $request->input('email'),
                // 'password' => $request->input('password'),
                'status' => $this->getPasswordStatus($status),
            ]);
            return response(["message" => "Successfully changed password. Please relogin from all of your devices."]);
                break;
        }
    }

    private function getPasswordStatus($status){
        $statuses = [
            'passwords.sent' => 'A password reset email has been sent.',
            'passwords.reset' => 'Successfully changed password.',
            'passwords.user' => 'Invalid User',
            'passwords.token' => 'Invalid Token',
            'passwords.throttled' => 'Too many password reset attempts.',
        ];
        return $statuses[$status];
    }

    /**
     * This function updates user's password provided
     * that the user is authenticated and knows the current password
     */
    public function update_password(Request $request)
    {
        $request->validate([
            'current_password' => ['required','string', new MatchOldPassword],
            'new_password' => 'required|string|min:8|confirmed'
        ]);
        $user = User::find(auth()->user()->id);
        $result = $user->update(['password'=> Hash::make($request->new_password)]);
        

        $this->logInfo('User\'s attempt to update password '. ($result ? 'was successful!' : 'failed!'), 'Password Management', $this->ACTION_UPDATE, );

        if($result){
            $user->sendPasswordChangedNotification();
            return response()->json(['message' => "Password Change Successful!"]);
        }
        return response()->json(['message' => "Failed to change password!"], 422);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Models\User;

use App\Http\Resources\UserResource;

use App\Traits\VerificationTrait;

class VerificationController extends Controller {
    use VerificationTrait;

    public function checkifverified(Request $request) {
        $user = $request->user();
        return response(["verified" => $user->email_verified_at]);
    }

    public function verify(Request $request, User $user) {
        if ($this->signatureExpired($request) || !$this->isValidRoute($request, $user)) {
            return response(["message" => "Invalid/Expired url."], 422);
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();

            $this->logInfo(
                "User verified email using verification link.",
                "Account Verification",
                $this->ACTION_UPDATE,
                new UserResource($user)
            );
        }

        return response([
            "verified" => $user->email_verified_at,
            "message" => "Email Verification successful!",
        ]);
    }

    public function resend(Request $request) {
        $user = auth()->user();
        if ($user->hasVerifiedEmail()) {
            return response([
                "message" => "Email already verified.",
                "isVerified" => true,
            ]);
        }

        $user->sendEmailVerification();

        $this->logInfo(
            "User requested new email verification link",
            "Account Verification",
            $this->ACTION_SEND_EMAIL,
            new UserResource($user)
        );

        return response([
            "message" => "Email verification link sent to your email.",
        ]);
    }
}

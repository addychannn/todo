<?php

namespace App\Traits;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\User;

trait VerificationTrait {
    protected $expire_delay = 30; // Delay in minutes
    public function createSignature(User $user) {
        $key = env("APP_KEY", "");
        $expires = Carbon::now()
            ->addMinutes($this->expire_delay)
            ->getTimestamp();
        $signature = hash_hmac("sha256", $user->hash . "_" . $user->email . "_" . $expires, $key);

        return "?expires=" .
            $expires .
            "&hash=" .
            hash_hmac("whirlpool", $user->email, $key) .
            "&signature=" .
            $signature;
    }

    public function signatureExpired(Request $request) {
        $expires = $request->input("expires");
        $timestamp = Carbon::createFromTimestamp($expires);
        $now = Carbon::now()->getTimestamp();

        return $now > $timestamp->getTimestamp();
    }

    public function isValidRoute(Request $request, User $user) {
        $key = env("APP_KEY", "");
        $expires = $request->input("expires");
        $signature = (string) $request->input("signature");

        $compare = (string) hash_hmac(
            "sha256",
            $user->hash . "_" . $user->email . "_" . $expires,
            $key
        );

        return hash_equals($signature, $compare);
    }
}

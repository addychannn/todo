<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request) {
        $userID = auth()->id();
        $currentID = $this->id;
        $sameUser = $this->id == $userID;
        $permissions = $sameUser
            ? $this->getAllPermissions()->pluck("name")
            : $this->getDirectPermissions()->pluck(["hash"]);
        $roles = $this->roles
            ->map(function ($role) {
                return [
                    "id" => $role->hash,
                    "name" => $role->name,
                ];
            })
            ->sortBy("name");

        return [
            "id" => $this->when(!$sameUser, $this->hash),
            "email" => $this->email,
            "username" => $this->username,
            "active" => !$this->disabled_at,
            "verified" => $this->email_verified_at,
            "profile" => new ProfilesResource($this->profile),

            "roles" => !$sameUser ? $roles->toArray() : $this->getRoleNames(),
            "permissions" => $permissions, // For own account (Profile)
        ];
    }
}

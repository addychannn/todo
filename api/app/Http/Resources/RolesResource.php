<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RolesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return[
            "id" => $this->hash,
            "name" => $this->name,
            "description" => $this->description,
            "protected" => $this->protected,
            "permissions" => $this->permissions->map(function ($permission) {
                return $permission->hash;
            }),
        ];
    }
}

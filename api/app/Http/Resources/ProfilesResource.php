<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Traits\Helpers;

class ProfilesResource extends JsonResource
{

    use Helpers;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'suffix' => $this->suffix,
            'full_name' => $this->fullName($this->first_name, $this->middle_name, $this->last_name, $this->suffix),
            'nickname' => $this->nickname,
            'gender' => new GenderResource($this->gender),
            'birthdate' => $this->birthdate,
            'image' => new ImageDisplayResource($this->image),
            'images' => ImageDisplayResource::collection($this->images),
            'addresses' => AddressResource::collection($this->addresses)->sortByDesc('isMain')->toArray(),
        ];
    }


}

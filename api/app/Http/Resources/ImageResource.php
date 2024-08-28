<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->hash,
            'alt' => $this->alt,
            'name' => $this->name,
            'path' => $this->path,
            'mime' => $this->mime,
            'extension' => $this->extension,
        ];
    }
}

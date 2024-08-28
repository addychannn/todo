<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageDisplayResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $noThumb = $this->extension == 'gif' || $this->extension == 'svg';
        return [
            'id' => $this->hash,
            'image' => route('image.display', ['hash' => $this->hash]),
            // 'image' => "image/$this->hash",
            'alt' => $this->alt,
            'status' => $this->status,
            'thumbnails' => [
                'small' => route($noThumb ? 'image.display' : 'image.thumb', [ 'thumbsize' => "sm", 'hash' => $this->hash,]),
                'medium' => route($noThumb ? 'image.display' : 'image.thumb', [ 'thumbsize' => "md", 'hash' => $this->hash,]),
                'large' => route($noThumb ? 'image.display' : 'image.thumb', [ 'thumbsize' => "lg", 'hash' => $this->hash,]),
                // 'small' => "image/sm/$this->hash",
                // 'medium' => "image/md/$this->hash",
                // 'large' => "image/lg/$this->hash",
            ],  
        ];
    }
}

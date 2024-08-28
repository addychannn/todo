<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $extracted = $this->extractNameUniqid($this->name, $this->ext);
        return [
            'id' => $this->hash,
            'name' => $extracted["name"],
            'ext' => $this->ext,
            'mime' => $this->mime,
            'size' => $this->size,
            'hash' => [
                'uniqid' => $extracted["uniqid"],
                'md5' => $this->hash_md5,
                'sha1' => $this->hash_sha1,
                'sha256' => $this->hash_sha256,
            ],
        ];
    }

    private function extractNameUniqid($name, $ext){
        $base = basename($name, ".".$ext);
        $tmp = explode('_', $base);
        $uniqid = array_pop($tmp);
        $name = implode("_", $tmp);

        return [
            'name' => $name.".".$ext,
            'uniqid' => $uniqid,
        ];
    }
}

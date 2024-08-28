<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LaptopResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name'=>$this->name ,
            'brand'=>$this->brands? new BrandResource($this->brands):null,
            'color'=>$this->colors? new ColorResource($this->colors):null,
            'price'=>$this->price,
            'hash'=>$this->hash,
            'deleted_at'=>$this->deleted_at ? Carbon::parse($this->deleted_at)->format('F d, Y h:i A') : null,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ListLaptopResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'hash' => $this->hash ?? null,
            'name' => $this->name ?? null,
            'price' => $this->price ?? 0.00,
            'brand' => $this->brands ? new BrandResource($this->brands) : null,
            'color' => $this->colors ? new ColorResource($this->colors) : null,
            'deleted_at' => $this->deleted_at ? Carbon::parse($this->deleted_at)->format('F d, Y h:i A') : null
        ];
    }
}

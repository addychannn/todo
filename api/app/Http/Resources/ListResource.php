<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'list_name'=>$this->list_name,
            'hash'=>$this->hash,
            'deleted_at'=>$this->deleted_at ? Carbon::parse($this->deleted_at)->format('F d, Y h:i A') : null,
            'tasks'=>$this->tasks ? TaskResource::collection($this->tasks) : null
        ];
    }
}

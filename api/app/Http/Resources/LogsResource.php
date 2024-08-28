<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\LogsTrait;
use Carbon\Carbon;

class LogsResource extends JsonResource
{
    use LogsTrait;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = $this->user ? json_decode(json_encode(new UserResource($this->user))) : null;
        return [
            'id' => $this->id,
            'user' => $user ? [
                'username' => $user->username,
                'email' => $user->email,
                'full_name' => $user->profile?->full_name,
            ] : null,
            'actor' => $this->actor,
            'action' => $this->action,
            'type' => $this->type,
            'level' => $this->level,
            'module' => $this->module,
            'old_data' => json_decode($this->old_data ?? ''),
            'new_data' => json_decode($this->new_data ?? ''),
            'date' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}

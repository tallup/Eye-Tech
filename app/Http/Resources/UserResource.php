<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'phone' => $this->phone,
            'profile_picture' => $this->profile_picture,
            'profile_picture_url' => $this->profile_picture
                ? asset('storage/'.$this->profile_picture)
                : null,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}

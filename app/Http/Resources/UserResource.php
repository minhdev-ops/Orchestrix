<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'role' => $this->roles->pluck('name')->first() ?? $this->role,
            'avatar' => $this->avatar,
            'phone' => $this->phone,
            'created_at' => $this->created_at,
        ];
    }
}

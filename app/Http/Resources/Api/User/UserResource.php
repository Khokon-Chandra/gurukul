<?php

namespace App\Http\Resources\Api\User;

use App\Http\Resources\Api\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'username'          => $this->username,
            'type'              => $this->type,
            'email'             => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'active'            => $this->active,
            'last_login_ip'     => $this->last_login_ip,
            'last_login_at'     => $this->last_login_at,
            'timezone'          => $this->timezone,
            'created_at'        => $this->created_at->format('d-F-Y h:i A'),
            'role'             => new RoleResource($this->roles->first()),
        ];
    }
}

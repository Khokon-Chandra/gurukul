<?php

namespace App\Http\Resources\Api;

use App\Http\Controllers\Api\RoleController;
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
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'type' => $this->type,
            'email' => $this->email,
            'active' => $this->active,
            'join_date' => $this->created_at,
            'last_login_ip' => $this->last_login_ip,
            'join_date' => $this->created_at,
            'timezone' => $this->timezone,
            'join_date' => $this->created_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'last_login_at' => $this->last_login_at,
            'department_id' => $this->department_id,
            'role' => $this->loadMissing('roles')
        ];
    }
}

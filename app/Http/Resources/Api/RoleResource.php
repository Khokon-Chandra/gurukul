<?php

namespace App\Http\Resources\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'users_count' => $this->getRoleUsersCount($this->name),
            'permissions' => PermissionChildResource::collection($this->permissions),
            'created_at' => $this->created_at->format('d-M-Y h:i A'),
            'updated_at' => $this->updated_at->format('d-M-Y h:i A'),
        ];
    }

    public function getRoleUsersCount($roleName): int
    {
      return  User::with('roles')->get()->filter(
            fn ($user) => $user->roles->where('name', $roleName)->toArray()
        )->count();
    }

}

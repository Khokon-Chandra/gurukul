<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupMemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'username'      => $this->username,
            'last_login_at' => $this->last_login_at,
            'status'        => $this->status,
            'department_id' => $this->department_id,
            'department'    => $this->department->name ?? 'N/A',
        ];
    }
}

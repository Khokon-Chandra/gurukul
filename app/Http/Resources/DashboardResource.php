<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'department_name' => $this->name,
            'total_user'      => $this->users_count,
            'more_users'      => '+' . $this->users_count - 3,
            'users_preview'   => DashboardUserResource::collection($this->users->take(3)),
        ];
    }
}

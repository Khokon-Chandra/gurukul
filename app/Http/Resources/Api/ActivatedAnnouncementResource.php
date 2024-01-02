<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivatedAnnouncementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'message'    => $this->message ?? '',
            'status'     => $this->status ?? '0',
            'department' => new DepartmentResource(@$this->department),
        ];
    }
}

<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
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
            'message'    => $this->message,
            'status'     => $this->status,
            'date'       => $this->created_at->format('d-M-Y h:i A'),
            'department' => $this->department->name ?? 'N/A',
            'created_by' => new UserResource($this->createdBy),
        ];


    }


}

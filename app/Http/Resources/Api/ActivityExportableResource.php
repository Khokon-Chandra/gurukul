<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityExportableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'NO' => $this->no,
            'DATE' => $this->created_at->format('d-F-Y h:i A'),
            'USERNAME' => $this->causer->username,
            'IP' => $this->properties['ip'] ?? null,
            'ACTIVITY' => $this->properties['activity'] ?? null,
            'TARGET' => $this->properties['target'] ?? null,
            'DESCRIPTION' => $this->description,
            'DEPARTMENT' => is_null($this->subject) ? null : $this->subject->department?->name
        ];
    }
}

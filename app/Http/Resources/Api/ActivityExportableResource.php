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
            'NO'          => $this->no,
            'DATE'        => $this->created_at->format('d-F-Y h:i A'),
            'USERNAME'    => $this->causer->username,
            'IP'          => json_decode($this->properties)->ip,
            'ACTIVITY'    => json_decode($this->properties)->activity,
            'TARGET'      => json_decode($this->properties)->target,
            'DESCRIPTION' => $this->description,
        ];
    }
}

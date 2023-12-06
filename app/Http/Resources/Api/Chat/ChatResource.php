<?php

namespace App\Http\Resources\Api\Chat;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
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
            'subject' => $this->subject,
            'receiver' => $this->receiver,
            'date' => $this->date,
            'time' => $this->time,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}

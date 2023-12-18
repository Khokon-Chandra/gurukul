<?php

namespace App\Http\Resources\Api\Chat;

use App\Http\Resources\Api\Cashflow\UserResource;
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
            'id'         => $this->id,
            'message'    => $this->subject,
            'date'       => $this->date,
            'time'       => $this->time,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user'       => new UserResource($this->user),
        ];
    }
}

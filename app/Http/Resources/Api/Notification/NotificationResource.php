<?php

namespace App\Http\Resources\Api\Notification;

use App\Http\Resources\Api\Cashflow\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            'subject'    => $this->subject,
            'date'       => Carbon::parse($this->date)->format('d-M-Y'),
            'time'       => $this->time,
            'created_by' => new UserResource($this->createdBy),
            'created_at' => $this->created_at->format('d-F-Y H:i:s'),
        ];
    }

    
}

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
            'id'            => $this->id,
            'department'    => $this->department->name,
            'name'          => $this->name,
            'amount'        => number_format($this->amount,2),
            'date'          => $this->created_at->format('d-M-Y h:i A'),
            'created_by'    => new UserResource($this->createdBy),
        ];
    }


}

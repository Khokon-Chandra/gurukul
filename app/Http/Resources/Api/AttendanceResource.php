<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\Api\Cashflow\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
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
            'username'   => $this->username,
            'amount' => $this->amount,
            'created_at' => $this->created_at->format('d-F-Y h:i:s A'),
            'updated_at' => $this->updated_at->format('d-F-Y h:i:s A'),
            'created_by' => new UserResource($this->createdBy),
        ];
    }
}

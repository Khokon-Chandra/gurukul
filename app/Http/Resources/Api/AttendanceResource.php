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
            'name'       => $this->name,
            'amount'     => number_format($this->amount,2),
            'date'       => $this->created_at->format('d-M-Y h:i A'),
            'department' => $this->department->name ?? 'N/A',
            'created_by' => new UserResource($this->createdBy),
        ];
    }
}

<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\Api\Cashflow\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CashflowResource extends JsonResource
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
            'created_by' => new UserResource($this->createdBy),
        ];
    }
}

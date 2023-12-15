<?php

namespace App\Http\Resources\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserIpResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'id'          => $this->id,
            'ip1'         => $this->ip1,
            'ip2'         => $this->ip2,
            'ip3'         => $this->ip3,
            'ip4'         => $this->ip4,
            'ip'          => $this->ip,
            'status'      => $this->whitelisted,
            'description' => $this->description,
            'date'        => $this->updated_at ? Carbon::parse($this->updated_at)->format('d-M-Y h:i A') : Carbon::parse($this->updated_at)->format('d-M-Y h:i A'),
        ];
    }
}

<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionChildResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if($this->children->count()){
            return [
                $this->name => PermissionChildResource::collection($this->children)
            ];
        }

        return [
            'id'          => $this->id,
            'type'         => $this->type,
            'guard_name' => $this->guard_name,
            'name' => $this->name,
            'description' => $this->display_name,
            'module_name' => $this->module_name,
            'parent_id'   => $this->parent_id,
            'created_at'  => $this->created_at->format('d-F-Y H:i:s'),
            'updated_at'  => $this->updated_at->format('d-F-Y H:i:s'),
        ];
    }
}

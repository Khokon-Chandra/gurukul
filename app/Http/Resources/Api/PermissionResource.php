<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

    if($this->getUsersPermissions()){
        foreach($this->getUsersPermissions() as $permission){
            return [
                $permission->module_name  => new PermissionChildResource($permission)
            ];
        }
    }

    return [

        ];

    }

    public function getUsersPermissions(){
        return $this->resource?->pivot?->pivotParent?->permissions;
    }
}

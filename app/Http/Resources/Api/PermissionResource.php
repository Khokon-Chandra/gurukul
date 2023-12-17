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

<<<<<<< HEAD
        return [
            $this->name => PermissionChildResource::collection($this->children),
=======
    if($this->getUsersPermissions()){
        foreach($this->getUsersPermissions() as $permission){
            return [
                $permission->module_name  => new PermissionChildResource($permission)
            ];
        }
    }

    return [

>>>>>>> d670d72405727b81ea77d3c9a23f5728372b8e04
        ];

    }

    public function getUsersPermissions(){
        return $this->resource?->pivot?->pivotParent?->permissions;
    }
}

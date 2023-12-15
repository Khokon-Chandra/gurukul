<?php

namespace App\Trait;

use App\Models\Permission;

trait HasPermissionsStructure
{
    public function pullAuthUserPermissionWithDataStructure()
    {
        $authUser = auth()->user();

        $permissions =  $authUser->roles->pluck('permissions')->flatten();

        $permissions = $permissions->merge($authUser->permissions);

        $permissionsWithDataStructure = (new Permission)
            ->modulePermission($permissions->pluck('id')->toArray());

        return $permissionsWithDataStructure->map(
            fn ($item) => $item->groupBy('sub_module_name')
                ->map(
                    fn ($innerItems) => $innerItems->map(
                        fn ($in) => $in['items'][$in['sub_module_name']]
                    )
                )
        );
    }

    public function pullAllPermissionsWithDataStructure(){

        $permissions = Permission::all();

        $permissionsWithDataStructure = (new Permission)
            ->modulePermission($permissions->pluck('id')->toArray());

        return $permissionsWithDataStructure->map(
            fn ($item) => $item->groupBy('sub_module_name')
                ->map(
                    fn ($innerItems) => $innerItems->map(
                        fn ($in) => $in['items'][$in['sub_module_name']]
                    )
                )
        );

    }
}

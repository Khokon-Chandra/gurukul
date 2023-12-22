<?php

namespace App\Trait;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait CanSort
{
    public function sortUserData(Request $request, $query){

        if($request->filled('sort_name')){
            return $query->orderBy('name', $request->sort_name);
        }

        if($request->filled('sort_username')){
            return $query->orderBy('username', $request->sort_username);
        }

        if($request->filled('sort_joindate')){
            return $query->orderBy('created_at', $request->sort_joindate);
        }

        if($request->filled('sort_role')){
           return $query->join('model_has_roles', function($join){
               $join->whereNotNull('model_has_roles.model_id');
                $join->on('users.id', '=', 'model_has_roles.model_id');

            })->select('users.*', 'roles.name as role_name', 'roles.guard_name as role_guard_name', 'roles.created_at as role_created_at')
                ->join('roles', 'model_has_roles.role_id', 'roles.id')
                ->orderBy('role_created_at', $request->sort_role);
            }
        }
}

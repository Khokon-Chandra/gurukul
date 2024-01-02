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
            $data = $query->join('model_has_roles', 'users.id', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.model_id', 'roles.id')
                ->select('user.username', 'roles.name', 'roles.guard_name');

            $data->orderBy('name', $request->sort_role);
        }

        if($request->filled('sort_department')){
            return $query->orderBy('department_id', $request->sort_department);
        }
    }
}

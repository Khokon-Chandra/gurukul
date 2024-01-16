<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role as ModelsRole;

class Role extends ModelsRole
{
    use HasFactory;


    public function scopeFilter($query, $request)
    {
        $query->when($request->name ?? false, function($query,$name){
            $query->where('name','like',"%{$name}");
        })

        ->when($request->department_id ?? false, function($query,$department){
            $query->whereHas('departments',function($query) use($department){
                $query->where('role_department.department_id',$department);
            });
        });
    }

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class,'role_department');
    }

}

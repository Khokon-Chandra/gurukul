<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role as ModelsRole;
use \Znck\Eloquent\Traits\BelongsToThrough;


class Role extends ModelsRole
{
    use HasFactory, BelongsToThrough;


    public function scopeFilter($query, $request)
    {
        $query->when($request->name ?? false, function ($query, $name) {
            $query->where('name', 'like', "%{$name}");
        })

            ->when($request->department_id ?? false, function ($query, $department) {
                $query->whereHas('departments', function ($query) use ($department) {
                    $query->where('role_department.department_id', $department);
                });
            });
    }

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'role_department');
    }


    public function department(): \Znck\Eloquent\Relations\BelongsToThrough
    {
        return $this->belongsToThrough(
            Department::class,
            RoleDepartment::class,
            'role_id',
            foreignKeyLookup: [RoleDepartment::class => 'id'],
            localKeyLookup: [RoleDepartment::class => 'role_id'],
        );
    }
}

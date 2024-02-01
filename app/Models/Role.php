<?php

namespace App\Models;

use App\Trait\BelongsToDepartment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role as ModelsRole;


class Role extends ModelsRole
{
    use HasFactory, BelongsToDepartment, SoftDeletes;


    public function scopeFilter($query, $request)
    {
        $query->when($request->name ?? false, function ($query, $name) {
            $query->where('name', 'like', "%{$name}");
        });
    }


    
}

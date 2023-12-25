<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class FilterDepartment implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->when(request('department_id') && request()->isMethod('get') ?request('department_id') : false, function($query, $department){
            $query->where('department_id', $department);
        });
    }
}

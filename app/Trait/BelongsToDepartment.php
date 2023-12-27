<?php

namespace App\Trait;

use App\Models\Department;
use App\Models\Scopes\FilterDepartment;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToDepartment
{


    /**
     * Get the department that owns the BelongsToDepartment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }


    protected static function booted()
    {
        // using seperate scope class
        static::addGlobalScope(new FilterDepartment);

    }
}

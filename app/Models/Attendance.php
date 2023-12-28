<?php

namespace App\Models;

use App\Trait\BelongsToDepartment;
use App\Trait\ParrentBoot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use HasFactory, SoftDeletes, ParrentBoot, BelongsToDepartment;

    protected $guarded = ['id'];


    public function scopeFilter($query, $request)
    {
        $query->when($request->name ?? false, fn ($query, $name) => $query
            ->where('name', 'like', "%$name%"))
            ->when($request->amount ?? false, fn ($query, $amount) => $query->where('amount', $amount))
            ->when($request->searchable_date_range ?? false, fn ($query, $dates) => $query->whereBetween('created_at', $dates))
            ->when($request->sort_by ?? false, fn ($query, $column) => $query->orderBy($column, $request->sort_type));
    }


}

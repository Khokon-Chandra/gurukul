<?php

namespace App\Models;

use App\Trait\BelongsToDepartment;
use App\Trait\ParrentBoot;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory, ParrentBoot, BelongsToDepartment;

    protected $fillable = [
        'message',
        'status',
        'created_by',
        'department_id',
    ];


    protected $casts = [
        'status' => 'boolean',
    ];


    

    public function scopeFilter(Builder $query, $request)
    {
        $query->when($request->message ?? false, fn ($query, $message) => $query
        ->where('message', 'like', "%$message%"))
        ->when($request->status ?? false, fn ($query, $status) => $query->where('status', $status))
        ->when($request->searchable_date_range ?? false, fn ($query, $dates) => $query->whereBetween('created_at', $dates))
        ->when($request->sort_by ?? false, fn ($query, $column) => $query->orderBy($column, $request->sort_type));
    }


   
}

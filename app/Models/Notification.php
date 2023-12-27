<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Notification extends Model
{
    use HasFactory,
        SoftDeletes;

    protected $guarded = ['id'];


    protected static function boot(): void
    {
        parent::boot();

        static::saving(function ($model) {
            $model->created_by = Auth::check() ? Auth::id() : null;
        });
    }



    public function scopeFilter($query, $request)
    {
        $query->where('department_id', $request->department_id)
            ->when($request->name ?? false, fn ($query, $name) => $query
            ->where('name', 'like', "%$name%"))
            ->when($request->amount ?? false, fn ($query, $amount) => $query->where('amount', $amount))
            ->when($request->searchable_date_range ?? false, fn ($query, $dates) => $query->whereBetween('created_at', $dates))
            ->when($request->sort_by ?? false, fn($query, $column) => $query->orderBy($column,$request->sort_type));
    }


    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'status',
        'created_by',
    ];


    protected $casts = [
        'status' => 'boolean',
    ];


    protected static function boot(): void
    {
        parent::boot();

        static::saving(function ($model) {
            $model->created_by = Auth::check() ? Auth::id() : null;
        });
    }


    public function scopeFilter(Builder $query, $request)
    {
        $query->when($request->message ?? false, fn ($query, $message) => $query
        ->where('message', 'like', "%$message%"))
        ->when($request->status ?? false, fn ($query, $status) => $query->where('status', $status))
        ->when($request->searchable_date_range ?? false, fn ($query, $dates) => $query->whereBetween('created_at', $dates))
        ->when($request->sort_by ?? false, fn ($query, $column) => $query->orderBy($column, $request->sort_type));
    }


    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}

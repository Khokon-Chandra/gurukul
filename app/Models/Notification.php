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
        $query->when($request->subject ?? false, fn($query, $subject) => $query
            ->where('subject','like',"%$subject%"))
            ->when($request->searchable_date_range ?? false, fn($query, $dates) => $query
            ->whereBetween('created_at', $dates));
    }


    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'created_by','id');
    }


}

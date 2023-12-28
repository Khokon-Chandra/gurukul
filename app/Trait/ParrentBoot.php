<?php

namespace App\Trait;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait ParrentBoot
{
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($query) {
            $query->created_by = Auth::id() ?? null;
        });

        static::deleting(function ($query) {
            $query->deleted_by = Auth::id() ?? null;
        });
    }


    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id')
        ->withDefault([
            'name' => 'N/A',
        ]);
    }



    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by')
            ->withDefault([
                'name' => 'N/A',
            ]);
    }
}
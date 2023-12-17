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

        static::deleting(function ($ip) {
            $ip->deleted_by = Auth::id();
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
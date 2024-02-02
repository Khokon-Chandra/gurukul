<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Znck\Eloquent\Traits\BelongsToThrough;


class Chat extends Model
{
    use HasFactory, BelongsToThrough, SoftDeletes;

    protected $fillable = [
        'user_id',
        'chatable_id',
        'chatable_type',
        'date',
        'time',
        'message'
    ];


    public function chatable(): MorphTo
    {
        return $this->morphTo();
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsToThrough(Department::class,User::class);
    }

}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'amount',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'deleted_by',
    ];


    public function scopeFilter($query, $request)
    {
        $query->when($request->username ?? false, fn($query, $username) => $query
            ->where('username','like',"%$username%"));
    }


    public function createdBy() : BelongsTo
    {
        return $this->belongsTo(User::class,'created_by','id');
    }
}

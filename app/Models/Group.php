<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'image',
    ];


    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }


    public function chats(): MorphMany
    {
        return $this->morphMany(Chat::class, 'chatable');
    }
}

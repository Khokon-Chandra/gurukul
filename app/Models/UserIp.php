<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class UserIp extends Model
{
    use HasFactory,
        SoftDeletes;

    protected $table = 'user_ips';

    public $timestamps = false;


    protected $casts = [
        'whitelisted' => 'boolean',
    ];


    protected $fillable = [
        'ip_address',
        'description',
        'whitelisted',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at',
    ];


    protected $appends = [
        'ip1',
        'ip2',
        'ip3',
        'ip4'
    ];


    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($ip) {
            $ip->deleted_by = Auth::id();
        });
    }


    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }



    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by')
            ->withDefault([
                'name' => 'N/A',
            ]);
    }



    public function scopeFilter($query, $request): void
    {
        $query->when($request->ip_address ?? false, fn ($query, $ip_address) => $query
            ->where('ip_address', $ip_address))
            ->when($request->whitelisted ?? false, fn ($query, $whitelisted) => $query->where('whitelisted', $whitelisted))
            ->when($request->description ?? false, fn ($query, $description) => $query->where('description', 'like', "%$description%"))
            ->when($request->sort_by ?? false, fn ($query, $column) => $query->orderBy($column, $request->sort_type));
    }


    /**
     * Get the Ip1.
     */
    protected function ip1(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $ipAddress = explode('.', $attributes['ip_address']);

                return $ipAddress[0] === '*' ? null : $ipAddress[0];
            },
        );
    }

    /**
     * Get the Ip2.
     */
    protected function ip2(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $ipAddress = explode('.', $attributes['ip_address']);

                return $ipAddress[1] === '*' ? null : $ipAddress[1];
            },
        );
    }

    /**
     * Get the Ip3.
     */
    protected function ip3(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $ipAddress = explode('.', $attributes['ip_address']);

                return $ipAddress[2] === '*' ? null : $ipAddress[2];
            },
        );
    }


    /**
     * Get the Ip4.
     */
    protected function ip4(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $ipAddress = explode('.', $attributes['ip_address']);

                return $ipAddress[3] === '*' ? null : $ipAddress[3];
            },
        );
    }
}

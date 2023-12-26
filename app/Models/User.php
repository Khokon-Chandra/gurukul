<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\UserIp;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens,
        SoftDeletes,
        HasFactory,
        Notifiable,
        HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'department_id',
        'username',
        'name',
        'email',
        'password',
        'last_login_ip',
        'timezone',
        'updated_by',
        'deleted_by',
        'deleted_at',
        'last_performed_at',
        'status',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status'   => 'boolean',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function ips()
    {
        return $this->hasMany(UserIp::class);
    }


    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }


    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class);
    }


    public function chats()
    {
        return $this->morphMany(Chat::class, 'chatable');
    }



    public function scopeFilter($query, $request)
    {
        $query->when(
            $request->name ?? false, fn ($query, $name) => $query
            ->where('name', 'like', "%$name%"))
            ->when($request->username ?? false, fn ($query, $username) => $query
                ->where('username', $username))
            ->when($request->email ?? false, fn ($query, $email) => $query
                ->where('email', 'like', "%$email%"))
            ->when($request->date_range ?? false, function ($query, $range) {
                $dates = explode(' to ',$range);
                $dates = [
                    @Carbon::parse($dates[0])->startOfDay()->format('Y-m-d H:i:s'),
                    @Carbon::parse($dates[1])->endOfDay()->format('Y-m-d H:i:s'),
                ];

                $query->whereBetween('created_at',$dates);
            });

    }

}

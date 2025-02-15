<?php

namespace App\Models;

use App\Casts\JDate;
use App\Events\UserCreated;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    public $fillable = ['name', 'mobile', 'email', 'role_id', 'status', 'password'];

    protected $hidden = ['role_id', 'password'];

    protected $casts = [
        'status' => 'boolean',
        'created_at' => JDate::class,
        'updated_at' => JDate::class,
    ];

    protected $dispatchesEvents = [
        'created' => UserCreated::class,
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

    public function OTPCode(): HasOne
    {
        return $this->hasOne(OTPCode::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class, 'user');
    }

    public function campains(): HasMany
    {
        return $this->hasMany(Campain::class);
    }

    public function hasRole($role)
    {
        Log::alert($role);
        if (is_numeric($role))
            return $this->roles()->where('id', $role)->exists();

        if (is_string($role))
            return $this->roles()->where('name', $role)->exists();


        return false;
    }
}

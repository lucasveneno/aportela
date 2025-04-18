<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory;
    use Notifiable;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'immutable_datetime',
        'region_id' => 'array',
    ];

    public function isAdmin(): bool
    {
        return $this->is_admin == '1';
    }

    public function hasRole($role): bool
    {
        return $this->role === $role;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function demands(): HasMany
    {
        return $this->hasMany(Demand::class);
    }
}

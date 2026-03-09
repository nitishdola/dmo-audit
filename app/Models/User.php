<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'mobile',
        'role',
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'role' => UserRole::class,
            'mobile_verified_at' => 'datetime',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function otpCodes(): HasMany
    {
        return $this->hasMany(OtpCode::class);
    }

    // ─── Accessors & Helpers ─────────────────────────────────────────────────

    public function otps()
    {
        return $this->hasMany(OtpCode::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isDMO(): bool
    {
        return $this->role === UserRole::DMO;
    }

    public function districts()
    {
        return $this->belongsToMany(
            District::class,
            'dmo_districts'
        );
    }
}
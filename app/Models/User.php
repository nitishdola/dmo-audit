<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\LiveAudit;
use Illuminate\Notifications\Notifiable;

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
            'role'                => UserRole::class,
            'mobile_verified_at'  => 'datetime',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function otpCodes(): HasMany
    {
        return $this->hasMany(OtpCode::class);
    }

    /** Alias kept for backwards compatibility. */
    public function otps(): HasMany
    {
        return $this->hasMany(OtpCode::class);
    }

    public function districts(): BelongsToMany
    {
        return $this->belongsToMany(
            District::class,
            'dmo_districts'
        );
    }

    /**
     * Independent Live Audits conducted by this DMO.
     * Foreign key: live_audits.submitted_by → users.id
     */
    public function liveAudits(): HasMany
    {
        return $this->hasMany(LiveAudit::class, 'submitted_by');
    }

    // ─── Role helpers ─────────────────────────────────────────────────────────

    /**
     * Check whether the user has a specific role.
     *
     * Accepts a UserRole enum, its string value, or multiple roles:
     *
     *   $user->hasRole(UserRole::ADMIN)
     *   $user->hasRole('admin')
     *   $user->hasRole(UserRole::ADMIN, UserRole::DMO)   // true if any match
     */
    public function hasRole(UserRole|string ...$roles): bool
    {
        foreach ($roles as $role) {
            $enum = $role instanceof UserRole
                ? $role
                : UserRole::tryFrom($role);

            if ($enum !== null && $this->role === $enum) {
                return true;
            }
        }

        return false;
    }

    /**
     * Assert that the user has ALL of the given roles.
     */
    public function hasAllRoles(UserRole|string ...$roles): bool
    {
        foreach ($roles as $role) {
            if (! $this->hasRole($role)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return the human-readable label for the user's role.
     *
     *   $user->roleLabel()  →  "District Medical Officer"
     */
    public function roleLabel(): string
    {
        return $this->role?->label() ?? '—';
    }

    /**
     * Return the short label for the user's role.
     *
     *   $user->roleShort()  →  "DMO"
     */
    public function roleShort(): string
    {
        return $this->role?->short() ?? '—';
    }

    /**
     * Return Tailwind badge classes for the user's role.
     *
     *   <span class="{{ $user->roleBadgeClass() }}">{{ $user->roleShort() }}</span>
     */
    public function roleBadgeClass(): string
    {
        return $this->role?->badgeClass() ?? 'bg-slate-100 text-slate-600';
    }

    // ─── Query scopes ─────────────────────────────────────────────────────────

    /**
     * Filter users by one or more roles.
     *
     * Replaces Spatie's User::role() — works with the plain `role` enum column.
     *
     *   User::role('dmo')->get()
     *   User::role(UserRole::ADMIN)->get()
     *   User::role(['admin', 'dmo'])->get()     // any of the listed roles
     *   User::role(UserRole::ADMIN, UserRole::DMO)->get()
     */
    public function scopeRole(
        \Illuminate\Database\Eloquent\Builder $query,
        UserRole|string|array ...$roles
    ): \Illuminate\Database\Eloquent\Builder {
        // Flatten: supports both ->role('dmo') and ->role(['dmo','admin'])
        $flat = collect($roles)->flatten()->map(function ($r) {
            return $r instanceof UserRole ? $r->value : (string) $r;
        })->unique()->values()->all();

        return $query->whereIn('role', $flat);
    }

    // ─── Convenience shorthand (preserved from original) ─────────────────────

    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::ADMIN);
    }

    public function isDMO(): bool
    {
        return $this->hasRole(UserRole::DMO);
    }
}

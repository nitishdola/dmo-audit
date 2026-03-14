<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case DMO   = 'dmo';

    // ── Display label ──────────────────────────────────────────────────────

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::DMO   => 'District Medical Officer',
        };
    }

    // ── Short label ────────────────────────────────────────────────────────

    public function short(): string
    {
        return match($this) {
            self::ADMIN => 'Admin',
            self::DMO   => 'DMO',
        };
    }

    // ── Badge colour (Tailwind) ────────────────────────────────────────────

    public function badgeClass(): string
    {
        return match($this) {
            self::ADMIN => 'bg-indigo-100 text-indigo-700',
            self::DMO   => 'bg-emerald-100 text-emerald-700',
        };
    }

    // ── Check if this value is in a given list ────────────────────────────

    public function in(self ...$roles): bool
    {
        return in_array($this, $roles, strict: true);
    }
}

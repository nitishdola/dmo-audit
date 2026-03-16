<?php

namespace App\Policies;

use App\Models\InfraAudit;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InfraAuditPolicy
{
    use HandlesAuthorization;

    public function view(User $user, InfraAudit $infraAudit): bool
    {
        return $user->hasAnyRole(['admin', 'supervisor'])
            || $infraAudit->submitted_by === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'supervisor', 'dmo']);
    }

    /**
     * Only supervisors and admins may edit a submitted audit.
     */
    public function update(User $user, InfraAudit $infraAudit): bool
    {
        return $user->hasAnyRole(['admin', 'supervisor']);
    }

    public function delete(User $user, InfraAudit $infraAudit): bool
    {
        return $user->hasAnyRole(['admin', 'supervisor']);
    }

    public function forceDelete(User $user, InfraAudit $infraAudit): bool
    {
        return $user->hasRole('admin');
    }
}

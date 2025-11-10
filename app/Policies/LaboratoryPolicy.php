<?php

namespace App\Policies;

use App\Models\Laboratory;
use App\Models\User;

class LaboratoryPolicy
{
    /**
     * Determine whether the user can view any laboratories.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole([User::ADMIN_ROLE]);
    }

    /**
     * Determine whether the user can view the laboratory.
     */
    public function view(User $user, Laboratory $laboratory): bool
    {
        return $user->hasRole([User::ADMIN_ROLE]);
    }

    /**
     * Determine whether the user can create laboratories.
     */
    public function create(User $user): bool
    {
        return $user->hasRole([User::ADMIN_ROLE]);
    }

    /**
     * Determine whether the user can update the laboratory.
     */
    public function update(User $user, Laboratory $laboratory): bool
    {
        return $user->hasRole([User::ADMIN_ROLE]);
    }

    /**
     * Determine whether the user can delete the laboratory.
     */
    public function delete(User $user, Laboratory $laboratory): bool
    {
        return $user->hasRole([User::ADMIN_ROLE]);
    }

    /**
     * Determine whether the user can restore the laboratory.
     */
    public function restore(User $user, Laboratory $laboratory): bool
    {
        return $user->hasRole([User::ADMIN_ROLE]);
    }

    /**
     * Determine whether the user can permanently delete the laboratory.
     */
    public function forceDelete(User $user, Laboratory $laboratory): bool
    {
        return $user->hasRole([User::ADMIN_ROLE]);
    }
}

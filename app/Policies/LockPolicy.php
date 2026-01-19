<?php

namespace App\Policies;

use App\Models\Lock;
use App\Models\User;

class LockPolicy
{
    /**
     * Determine whether the user can view any locks.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole([User::ADMIN_ROLE]);
    }

    /**
     * Determine whether the user can view the lock.
     */
    public function view(User $user, Lock $lock): bool
    {
        return $user->hasRole([User::ADMIN_ROLE]);
    }

    /**
     * Determine whether the user can create locks.
     */
    public function create(User $user): bool
    {
        return $user->hasRole([User::ADMIN_ROLE]);
    }

    /**
     * Determine whether the user can update the lock.
     */
    public function update(User $user, Lock $lock): bool
    {
        return $user->hasRole([User::ADMIN_ROLE]);
    }

    /**
     * Determine whether the user can delete the lock.
     */
    public function delete(User $user, Lock $lock): bool
    {
        return $user->hasRole([User::ADMIN_ROLE]);
    }

    /**
     * Determine whether the user can restore the lock.
     */
    public function restore(User $user, Lock $lock): bool
    {
        return $user->hasRole([User::ADMIN_ROLE]);
    }

    /**
     * Determine whether the user can permanently delete the lock.
     */
    public function forceDelete(User $user, Lock $lock): bool
    {
        return $user->hasRole([User::ADMIN_ROLE]);
    }
}

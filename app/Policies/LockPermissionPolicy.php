<?php

namespace App\Policies;

use App\Models\LockPermission;
use App\Models\User;

class LockPermissionPolicy
{
    /**
     * Determine whether the user can view any lock permissions.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole([User::ADMIN_ROLE]);
    }

    /**
     * Determine whether the user can view the lock permission.
     */
    public function view(User $user, LockPermission $lockPermission): bool
    {
        return $user->hasRole([User::ADMIN_ROLE]);
    }

    /**
     * Determine whether the user can create lock permissions.
     */
    public function create(User $user): bool
    {
        return $user->hasRole([User::ADMIN_ROLE]);
    }

    /**
     * Determine whether the user can update the lock permission.
     */
    public function update(User $user, LockPermission $lockPermission): bool
    {
        return $user->hasRole([User::ADMIN_ROLE]);
    }

    /**
     * Determine whether the user can delete the lock permission.
     */
    public function delete(User $user, LockPermission $lockPermission): bool
    {
        return $user->hasRole([User::ADMIN_ROLE]);
    }

    /**
     * Determine whether the user can restore the lock permission.
     */
    public function restore(User $user, LockPermission $lockPermission): bool
    {
        return $user->hasRole([User::ADMIN_ROLE]);
    }

    /**
     * Determine whether the user can permanently delete the lock permission.
     */
    public function forceDelete(User $user, LockPermission $lockPermission): bool
    {
        return $user->hasRole([User::ADMIN_ROLE]);
    }
}

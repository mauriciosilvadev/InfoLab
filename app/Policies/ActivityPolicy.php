<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;

class ActivityPolicy
{
    /**
     * Determine whether the activity can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole([User::ADMIN_ROLE]);
    }

    /**
     * Determine whether the activity can view the model.
     */
    public function view(User $user, Activity $activity): bool
    {
        return $user->hasRole([User::ADMIN_ROLE]);
    }

    /**
     * Determine whether the activity can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole([User::ADMIN_ROLE]);
    }

    /**
     * Determine whether the activity can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->hasRole([User::ADMIN_ROLE]);
    }

    /**
     * Determine whether the activity can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->hasRole([User::ADMIN_ROLE]);
    }

    /**
     * Determine whether the activity can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->hasRole([User::ADMIN_ROLE]);
    }

    /**
     * Determine whether the activity can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->hasRole([User::ADMIN_ROLE]);
    }
}

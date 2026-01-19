<?php

namespace App\Observers;

use App\Models\LockPermission;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $this->linkLockPermissions($user);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Se email ou alternative_email mudaram, tentar vincular
        if ($user->wasChanged(['email', 'alternative_email'])) {
            $this->linkLockPermissions($user);
        }
    }

    /**
     * Link lock permissions to the user based on email.
     */
    private function linkLockPermissions(User $user): void
    {
        $permissions = LockPermission::whereNull('user_id')
            ->where(function ($query) use ($user) {
                $query->where('email', $user->email);
                if ($user->alternative_email) {
                    $query->orWhere('email', $user->alternative_email);
                }
            })
            ->get();

        foreach ($permissions as $permission) {
            $permission->user_id = $user->id;
            $permission->saveQuietly();
        }
    }
}

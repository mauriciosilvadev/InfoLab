<?php

namespace App\Observers;

use App\Models\LockPermission;
use App\Models\User;

class LockPermissionObserver
{
    /**
     * Handle the LockPermission "created" event.
     */
    public function created(LockPermission $lockPermission): void
    {
        $this->tryLinkUser($lockPermission);
    }

    /**
     * Handle the LockPermission "updated" event.
     */
    public function updated(LockPermission $lockPermission): void
    {
        if ($lockPermission->wasChanged('email')) {
            $this->tryLinkUser($lockPermission);
        }
    }

    /**
     * Try to link the lock permission to a user based on email.
     */
    private function tryLinkUser(LockPermission $lockPermission): void
    {
        $user = User::where('email', $lockPermission->email)
            ->orWhere('alternative_email', $lockPermission->email)
            ->first();

        if ($user && $lockPermission->user_id !== $user->id) {
            $lockPermission->user_id = $user->id;
            $lockPermission->saveQuietly();
        }
    }
}

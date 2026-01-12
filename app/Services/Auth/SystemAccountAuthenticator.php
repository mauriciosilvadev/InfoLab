<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SystemAccountAuthenticator
{
    public function find(string $username): ?User
    {
        if (! in_array($username, User::SYSTEM_ROLES)) {
            return null;
        }

        $user = User::query()->where('username', $username)->first();

        if (! $user) {
            return null;
        }

        return $user->hasRole(User::SYSTEM_ROLES) ? $user : null;
    }

    public function verifyPassword(User $user, #[\SensitiveParameter] string $password): bool
    {
        if (! $user->getAuthPassword()) {
            return false;
        }

        return Hash::check($password, $user->getAuthPassword());
    }
}

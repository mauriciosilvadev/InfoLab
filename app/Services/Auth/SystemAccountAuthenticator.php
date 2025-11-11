<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SystemAccountAuthenticator
{
    /**
     * @return array<int, string>
     */
    private function privilegedRoles(): array
    {
        return [
            User::ADMIN_ROLE,
            User::VIGIA_ROLE,
            User::SUGRAD_ROLE,
        ];
    }

    public function find(string $username): ?User
    {
        $user = User::query()->where('username', $username)->first();

        if (! $user) {
            return null;
        }

        return $user->hasRole($this->privilegedRoles()) ? $user : null;
    }

    public function verifyPassword(User $user, #[\SensitiveParameter] string $password): bool
    {
        if (! $user->getAuthPassword()) {
            return false;
        }

        return Hash::check($password, $user->getAuthPassword());
    }
}

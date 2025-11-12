<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class UserSynchronizer
{
    /**
     * @param  array{username:string,name:?string,email:?string,raw?:array<string,mixed>}  $directoryUser
     */
    public function sync(array $directoryUser): User
    {
        $user = User::query()->firstWhere('username', $directoryUser['username']);
        $now = now();

        $attributes = Arr::where([
            'name' => $directoryUser['name'] ?? null,
            'email' => $directoryUser['email'] ?? null,
            'alternative_email' => $directoryUser['alternative_email'] ?? null,
            'cpf' => $directoryUser['cpf'] ?? null,
            'matricula' => $directoryUser['matricula'] ?? null,
            'email_verified_at' => $now,
        ], static fn ($value) => $value !== null);

        if ($user) {
            $user->fill($attributes);

            if ($user->isDirty()) {
                $user->save();
            }

            return $user;
        }

        $user = User::create([
            'username' => $directoryUser['username'],
            'name' => $directoryUser['name'] ?? $directoryUser['username'],
            'email' => $directoryUser['email'] ?? null,
            'alternative_email' => $directoryUser['alternative_email'] ?? null,
            'cpf' => $directoryUser['cpf'] ?? null,
            'matricula' => $directoryUser['matricula'] ?? null,
            'password' => Str::random(32),
            'email_verified_at' => $now,
        ]);

        $user->assignRole(User::USER_ROLE);

        return $user;
    }
}

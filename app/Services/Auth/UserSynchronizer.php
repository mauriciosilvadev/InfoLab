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

        $attributes = [
            'name' => $directoryUser['name'] ?? null,
            'email' => $directoryUser['email'] ?? null,
            'email_verified_at' => $now,
        ];

        if ($user) {
            $user->fill(Arr::where($attributes, static fn ($value) => $value !== null));

            if ($user->isDirty()) {
                $user->save();
            }

            return $user;
        }

        return User::create([
            'username' => $directoryUser['username'],
            'name' => $directoryUser['name'] ?? $directoryUser['username'],
            'email' => $directoryUser['email'] ?? null,
            'password' => Str::random(32),
            'email_verified_at' => $now,
        ]);
    }
}

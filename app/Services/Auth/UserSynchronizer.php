<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\UserPreregistration;
use Illuminate\Support\Arr;

class UserSynchronizer
{
    /**
     * @param  array{username:string,name:?string,email:?string,is_teacher:bool,raw?:array<string,mixed>}  $LdapUser
     */
    public function sync(array $LdapUser): User
    {
        $user = User::query()->firstWhere('username', $LdapUser['username']);
        $now = now();

        $attributes = Arr::where([
            'name' => $LdapUser['name'] ?? null,
            'email' => $LdapUser['email'] ?? null,
            'alternative_email' => $LdapUser['alternative_email'] ?? null,
            'cpf' => $LdapUser['cpf'] ?? null,
            'matricula' => $LdapUser['matricula'] ?? null,
            'email_verified_at' => $now,
        ], static fn ($value) => $value !== null);

        if ($user) {
            $user->fill($attributes);

            if ($user->isDirty()) {
                $user->save();
            }

            return $user;
        }

        $role = $this->determineRole($LdapUser);

        $user = User::create([
            'username' => $LdapUser['username'],
            'name' => $LdapUser['name'] ?? $LdapUser['username'],
            'email' => $LdapUser['email'] ?? null,
            'alternative_email' => $LdapUser['alternative_email'] ?? null,
            'cpf' => $LdapUser['cpf'] ?? null,
            'matricula' => $LdapUser['matricula'] ?? null,
            'email_verified_at' => $now,
        ]);

        $user->assignRole($role);

        return $user;
    }

    /**
     * Determine the role for a new user based on preregistration or LDAP data.
     *
     * @param  array{username:string,email:?string,is_teacher:bool}  $LdapUser
     */
    private function determineRole(array $LdapUser): string
    {
        $email = $LdapUser['email'] ?? null;

        if ($email) {
            $preregistration = UserPreregistration::byEmail($email)->first();

            if (! $preregistration) {
                $alternativeEmail = $LdapUser['alternative_email'] ?? null;

                if ($alternativeEmail) {
                    $preregistration = UserPreregistration::byEmail($LdapUser['alternative_email'] ?? null)->first();
                }
            }

            if ($preregistration) {
                $role = $preregistration->role;
                $preregistration->delete();

                return $role;
            }
        }

        if ($LdapUser['is_teacher'] ?? false) {
            return User::TEACHER_ROLE;
        }

        return User::USER_ROLE;
    }
}

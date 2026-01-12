<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'admin',
                'email' => 'mauricio.s.dev@gmail.com',
                'role' => User::ADMIN_ROLE,
            ],
            [
                'name' => 'vigia',
                'email' => 'vigia@vigia.com',
                'role' => User::VIGIA_ROLE,
            ],
            [
                'name' => 'sugrad',
                'email' => 'sugrad@sugrad.com',
                'role' => User::SUGRAD_ROLE,
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['username' => $userData['name']],
                [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'alternative_email' => $userData['email'],
                    'password' => Hash::make('123123'),
                    'email_verified_at' => now(),
                ]
            );

            if (! $user->hasRole($userData['role'])) {
                $user->assignRole($userData['role']);
            }
        }
    }
}

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
            ],
            [
                'name' => 'vigia',
                'email' => 'vigia@vigia.com',
            ],
            [
                'name' => 'sugrad',
                'email' => 'sugrad@sugrad.com',
            ],
            [
                'name' => 'user',
                'email' => 'user@user.com',
            ],
        ];

        foreach ($users as $user) {
            $user = User::create([
                'name' => $user['name'],
                'username' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make('123123'),
                'email_verified_at' => now(),
            ]);

            $user->assignRole($user['name']);
        }
    }
}

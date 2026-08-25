<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['name' => 'Admin', 'email' => 'admin@devquestgamify.test', 'role' => UserRole::ADMIN],
            ['name' => 'Product Owner', 'email' => 'po@devquestgamify.test', 'role' => UserRole::PRODUCT_OWNER],
            ['name' => 'Developer', 'email' => 'dev@devquestgamify.test', 'role' => UserRole::DEVELOPER],
        ];

        foreach ($accounts as $account) {
            User::firstOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'role' => $account['role'],
                    'password' => 'password',
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'EyeTech Admin',
                'password' => Hash::make('REDACTED'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ],
        );

        $admin->syncRoles(['admin']);
    }
}

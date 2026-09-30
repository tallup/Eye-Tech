<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('seed.admin_email');
        $password = config('seed.admin_password');

        if (! $password) {
            $password = Str::random(16);
            $this->command?->warn("SEED_ADMIN_PASSWORD not set. Generated admin password for {$email}: {$password}");
        }

        $admin = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'EyeTech Admin',
                'password' => Hash::make($password),
                'role' => 'admin',
                'email_verified_at' => now(),
            ],
        );

        $admin->syncRoles(['admin']);
    }
}

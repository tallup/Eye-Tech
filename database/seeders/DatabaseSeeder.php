<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Run our custom seeders
        $this->call([
            RolesSeeder::class,
            AdminUserSeeder::class,
            CategorySeeder::class,
            SupplierSeeder::class,
            ServiceSeeder::class,
            ProductSeeder::class,
        ]);
    }
}

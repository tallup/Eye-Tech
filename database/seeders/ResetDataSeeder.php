<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\ServiceRequest;

class ResetDataSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        ServiceRequest::truncate();
        Product::query()->delete();
        Service::query()->delete();
        Category::query()->delete();
        Supplier::query()->delete();

        // Run seeders
        $this->call([
            CategorySeeder::class,
            SupplierSeeder::class,
            ServiceSeeder::class,
            ProductSeeder::class,
        ]);
    }
}

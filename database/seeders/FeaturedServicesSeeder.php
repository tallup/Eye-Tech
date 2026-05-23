<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class FeaturedServicesSeeder extends Seeder
{
    public function run(): void
    {
        $featured = [
            'Phone Unlocking Service',
            'App Installation & Configuration',
            'Cloud Setup & Sync',
        ];

        Service::query()->update(['is_featured' => false]);
        Service::query()->whereIn('name', $featured)->update(['is_featured' => true]);
    }
}

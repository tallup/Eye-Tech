<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Accessories',
                'slug' => 'accessories',
                'description' => 'Mobile phone accessories including cases, screen protectors, chargers, and more',
                'icon' => '📱',
                'is_active' => true,
            ],
            [
                'name' => 'Screen Protectors',
                'slug' => 'screen-protectors',
                'description' => 'High-quality tempered glass and film screen protectors for all devices',
                'icon' => '🛡️',
                'is_active' => true,
            ],
            [
                'name' => 'Chargers & Cables',
                'slug' => 'chargers-cables',
                'description' => 'Fast charging cables, wireless chargers, and power banks',
                'icon' => '⚡',
                'is_active' => true,
            ],
            [
                'name' => 'Phone Cases',
                'slug' => 'phone-cases',
                'description' => 'Protective cases, covers, and bumpers for all phone models',
                'icon' => '📱',
                'is_active' => true,
            ],
            [
                'name' => 'Audio & Headphones',
                'slug' => 'audio-headphones',
                'description' => 'Earbuds, headphones, and audio accessories',
                'icon' => '🎧',
                'is_active' => true,
            ],
            [
                'name' => 'Gaming Accessories',
                'slug' => 'gaming-accessories',
                'description' => 'Gaming controllers, grips, and mobile gaming accessories',
                'icon' => '🎮',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }
    }
}
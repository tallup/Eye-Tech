<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Get category IDs dynamically
        $screenProtectorsCategory = \App\Models\Category::where('slug', 'screen-protectors')->first();
        $accessoriesCategory = \App\Models\Category::where('slug', 'accessories')->first();
        $chargersCategory = \App\Models\Category::where('slug', 'chargers-cables')->first();
        $phoneCasesCategory = \App\Models\Category::where('slug', 'phone-cases')->first();
        $audioCategory = \App\Models\Category::where('slug', 'audio-headphones')->first();
        $gamingCategory = \App\Models\Category::where('slug', 'gaming-accessories')->first();

        // Get supplier IDs dynamically
        $supplier1 = \App\Models\Supplier::first();
        $supplier2 = \App\Models\Supplier::skip(1)->first();
        $supplier3 = \App\Models\Supplier::skip(2)->first();
        $supplier4 = \App\Models\Supplier::skip(3)->first();

        $products = [
            // Screen Protectors
            [
                'name' => 'iPhone 15 Pro Max Tempered Glass Screen Protector',
                'sku' => 'IPH15PM-SP-001',
                'description' => 'Premium tempered glass screen protector with 9H hardness, anti-fingerprint coating, and crystal clear clarity.',
                'category_id' => $screenProtectorsCategory->id,
                'supplier_id' => $supplier1->id,
                'cost_price' => 8.50,
                'selling_price' => 25.00,
                'stock_quantity' => 100,
                'min_stock_level' => 20,
                'brand' => 'Apple',
                'model' => 'iPhone 15 Pro Max',
                'image' => 'products/screen-protector-iphone.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra Screen Protector',
                'sku' => 'SGS24U-SP-001',
                'description' => 'Ultra-thin tempered glass with edge-to-edge coverage, anti-scratch protection, and easy installation.',
                'category_id' => $screenProtectorsCategory->id,
                'supplier_id' => $supplier2->id,
                'cost_price' => 7.00,
                'selling_price' => 22.00,
                'stock_quantity' => 80,
                'min_stock_level' => 15,
                'brand' => 'Samsung',
                'model' => 'Galaxy S24 Ultra',
                'image' => 'products/screen-protector-samsung.jpg',
                'is_active' => true,
            ],
            
            // Phone Cases
            [
                'name' => 'iPhone 15 Pro Clear Case with MagSafe',
                'sku' => 'IPH15P-CASE-001',
                'description' => 'Crystal clear protective case with MagSafe compatibility, military-grade protection, and wireless charging support.',
                'category_id' => $phoneCasesCategory->id,
                'supplier_id' => $supplier1->id,
                'cost_price' => 12.00,
                'selling_price' => 35.00,
                'stock_quantity' => 60,
                'min_stock_level' => 10,
                'brand' => 'Apple',
                'model' => 'iPhone 15 Pro',
                'image' => 'products/phone-case-iphone.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra Rugged Case',
                'sku' => 'SGS24U-CASE-001',
                'description' => 'Heavy-duty protective case with shock absorption, dust protection, and built-in kickstand.',
                'category_id' => $phoneCasesCategory->id,
                'supplier_id' => $supplier2->id,
                'cost_price' => 15.00,
                'selling_price' => 40.00,
                'stock_quantity' => 45,
                'min_stock_level' => 8,
                'brand' => 'Samsung',
                'model' => 'Galaxy S24 Ultra',
                'image' => 'products/screen-protector-samsung.jpg',
                'is_active' => true,
            ],
            
            // Chargers & Cables
            [
                'name' => 'USB-C Fast Charging Cable (2m)',
                'sku' => 'USBC-CABLE-001',
                'description' => 'High-speed USB-C charging cable with 100W power delivery, braided nylon design, and gold-plated connectors.',
                'category_id' => $chargersCategory->id,
                'supplier_id' => $supplier3->id,
                'cost_price' => 5.00,
                'selling_price' => 18.00,
                'stock_quantity' => 200,
                'min_stock_level' => 30,
                'brand' => 'Generic',
                'model' => 'Universal',
                'image' => 'products/usb-cable.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Wireless Charging Pad (15W)',
                'sku' => 'WIRELESS-PAD-001',
                'description' => 'Fast wireless charging pad with LED indicator, anti-slip design, and universal compatibility.',
                'category_id' => $chargersCategory->id,
                'supplier_id' => $supplier4->id,
                'cost_price' => 18.00,
                'selling_price' => 45.00,
                'stock_quantity' => 50,
                'min_stock_level' => 10,
                'brand' => 'Generic',
                'model' => 'Universal',
                'image' => 'products/usb-cable.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Power Bank 20000mAh with PD',
                'sku' => 'POWERBANK-001',
                'description' => 'High-capacity power bank with Power Delivery, dual USB ports, and fast charging for all devices.',
                'category_id' => $chargersCategory->id,
                'supplier_id' => $supplier1->id,
                'cost_price' => 25.00,
                'selling_price' => 65.00,
                'stock_quantity' => 30,
                'min_stock_level' => 5,
                'brand' => 'Generic',
                'model' => 'Universal',
                'image' => 'products/usb-cable.jpg',
                'is_active' => true,
            ],
            
            // Audio & Headphones
            [
                'name' => 'Bluetooth Earbuds Pro',
                'sku' => 'EARBUDS-PRO-001',
                'description' => 'Premium wireless earbuds with active noise cancellation, 8-hour battery life, and crystal clear sound.',
                'category_id' => $audioCategory->id,
                'supplier_id' => $supplier2->id,
                'cost_price' => 35.00,
                'selling_price' => 85.00,
                'stock_quantity' => 40,
                'min_stock_level' => 8,
                'brand' => 'Generic',
                'model' => 'Universal',
                'image' => 'products/usb-cable.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Gaming Headset with Mic',
                'sku' => 'GAMING-HEADSET-001',
                'description' => 'Professional gaming headset with 7.1 surround sound, noise-canceling microphone, and RGB lighting.',
                'category_id' => $audioCategory->id,
                'supplier_id' => $supplier3->id,
                'cost_price' => 45.00,
                'selling_price' => 120.00,
                'stock_quantity' => 25,
                'min_stock_level' => 5,
                'brand' => 'Generic',
                'model' => 'Universal',
                'image' => 'products/usb-cable.jpg',
                'is_active' => true,
            ],
            
            // Gaming Accessories
            [
                'name' => 'Mobile Gaming Controller',
                'sku' => 'GAME-CONTROLLER-001',
                'description' => 'Bluetooth gaming controller with ergonomic design, low latency, and compatibility with all mobile games.',
                'category_id' => $gamingCategory->id,
                'supplier_id' => $supplier4->id,
                'cost_price' => 30.00,
                'selling_price' => 75.00,
                'stock_quantity' => 35,
                'min_stock_level' => 7,
                'brand' => 'Generic',
                'model' => 'Universal',
                'image' => 'products/usb-cable.jpg',
                'is_active' => true,
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}
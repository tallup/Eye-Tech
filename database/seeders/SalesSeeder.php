<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some products and users for the sales
        $products = \App\Models\Product::where('is_active', true)->get();
        $adminUser = \App\Models\User::where('email', config('seed.admin_email'))->first();

        if ($products->isEmpty() || !$adminUser) {
            $this->command->info('No products or admin user found. Please run ProductSeeder and AdminUserSeeder first.');
            return;
        }

        // Create sample sales transactions
        $salesData = [
            [
                'customer_name' => 'John Smith',
                'customer_phone' => '+220 123 4567',
                'customer_email' => 'john.smith@example.com',
                'payment_method' => 'cash',
                'status' => 'completed',
                'subtotal' => 450.00,
                'tax_amount' => 22.50,
                'discount_amount' => 25.00,
                'total_amount' => 447.50,
                'notes' => 'Regular customer, gave 5% discount',
                'items' => [
                    ['product' => 0, 'quantity' => 2, 'unit_price' => 125.00, 'discount_amount' => 25.00],
                    ['product' => 1, 'quantity' => 1, 'unit_price' => 200.00, 'discount_amount' => 0.00],
                ]
            ],
            [
                'customer_name' => 'Fatou Jallow',
                'customer_phone' => '+220 987 6543',
                'customer_email' => 'fatou.jallow@example.com',
                'payment_method' => 'mobile_money',
                'status' => 'completed',
                'subtotal' => 320.00,
                'tax_amount' => 16.00,
                'discount_amount' => 0.00,
                'total_amount' => 336.00,
                'notes' => 'Mobile money payment via QMoney',
                'items' => [
                    ['product' => 2, 'quantity' => 1, 'unit_price' => 150.00, 'discount_amount' => 0.00],
                    ['product' => 3, 'quantity' => 1, 'unit_price' => 170.00, 'discount_amount' => 0.00],
                ]
            ],
            [
                'customer_name' => 'Modou Njie',
                'customer_phone' => '+220 555 1234',
                'customer_email' => null,
                'payment_method' => 'card',
                'status' => 'completed',
                'subtotal' => 275.00,
                'tax_amount' => 13.75,
                'discount_amount' => 15.00,
                'total_amount' => 273.75,
                'notes' => 'Card payment, customer requested receipt',
                'items' => [
                    ['product' => 4, 'quantity' => 3, 'unit_price' => 75.00, 'discount_amount' => 15.00],
                    ['product' => 5, 'quantity' => 1, 'unit_price' => 50.00, 'discount_amount' => 0.00],
                ]
            ],
            [
                'customer_name' => 'Aminata Sarr',
                'customer_phone' => '+220 444 7890',
                'customer_email' => 'aminata.sarr@example.com',
                'payment_method' => 'bank_transfer',
                'status' => 'pending',
                'subtotal' => 180.00,
                'tax_amount' => 9.00,
                'discount_amount' => 0.00,
                'total_amount' => 189.00,
                'notes' => 'Awaiting bank transfer confirmation',
                'items' => [
                    ['product' => 6, 'quantity' => 2, 'unit_price' => 90.00, 'discount_amount' => 0.00],
                ]
            ],
            [
                'customer_name' => 'Ousmane Badjie',
                'customer_phone' => '+220 333 5678',
                'customer_email' => null,
                'payment_method' => 'cash',
                'status' => 'completed',
                'subtotal' => 500.00,
                'tax_amount' => 25.00,
                'discount_amount' => 50.00,
                'total_amount' => 475.00,
                'notes' => 'Bulk purchase discount applied',
                'items' => [
                    ['product' => 7, 'quantity' => 1, 'unit_price' => 300.00, 'discount_amount' => 30.00],
                    ['product' => 8, 'quantity' => 1, 'unit_price' => 200.00, 'discount_amount' => 20.00],
                ]
            ],
        ];

        foreach ($salesData as $saleData) {
            $sale = \App\Models\Sales::create([
                'customer_name' => $saleData['customer_name'],
                'customer_phone' => $saleData['customer_phone'],
                'customer_email' => $saleData['customer_email'],
                'payment_method' => $saleData['payment_method'],
                'status' => $saleData['status'],
                'subtotal' => $saleData['subtotal'],
                'tax_amount' => $saleData['tax_amount'],
                'discount_amount' => $saleData['discount_amount'],
                'total_amount' => $saleData['total_amount'],
                'notes' => $saleData['notes'],
                'user_id' => $adminUser->id,
            ]);

            // Create sales items
            foreach ($saleData['items'] as $itemData) {
                $product = $products[$itemData['product']] ?? $products->first();
                
                \App\Models\SalesItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'item_name' => $product->name,
                    'item_sku' => $product->sku,
                    'item_description' => $product->description,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'discount_amount' => $itemData['discount_amount'],
                    'total_price' => ($itemData['quantity'] * $itemData['unit_price']) - $itemData['discount_amount'],
                ]);

                // Update product stock
                $product->decrement('stock_quantity', $itemData['quantity']);
            }
        }

        $this->command->info('Sales transactions created successfully!');
    }
}

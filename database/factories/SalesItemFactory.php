<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Sales;
use App\Models\SalesItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalesItem>
 */
class SalesItemFactory extends Factory
{
    protected $model = SalesItem::class;

    public function definition(): array
    {
        return [
            'sale_id' => Sales::factory(),
            'product_id' => Product::factory(),
            'item_name' => $this->faker->words(2, true),
            'item_sku' => strtoupper($this->faker->bothify('SKU-####')),
            'item_description' => null,
            'quantity' => 1,
            'unit_price' => 50.00,
            'discount_amount' => 0,
            'total_price' => 50.00,
        ];
    }
}

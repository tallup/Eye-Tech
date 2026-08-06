<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(3, true),
            'sku' => strtoupper($this->faker->unique()->bothify('??-####')),
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
            'cost_price' => $this->faker->randomFloat(2, 10, 200),
            'selling_price' => $this->faker->randomFloat(2, 20, 400),
            'stock_quantity' => $this->faker->numberBetween(0, 50),
            'min_stock_level' => $this->faker->numberBetween(0, 5),
            'is_active' => true,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Sales;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sales>
 */
class SalesFactory extends Factory
{
    protected $model = Sales::class;

    public function definition(): array
    {
        return [
            'sale_number' => 'S-'.now()->format('Ymd').'-'.$this->faker->unique()->numerify('####'),
            'customer_name' => $this->faker->optional()->name(),
            'customer_phone' => $this->faker->optional()->phoneNumber(),
            'customer_email' => $this->faker->optional()->safeEmail(),
            'payment_method' => $this->faker->randomElement(['cash', 'card', 'mobile_money', 'bank_transfer']),
            'status' => 'completed',
            'subtotal' => 100.00,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 100.00,
            'notes' => null,
            'user_id' => User::factory(),
        ];
    }
}

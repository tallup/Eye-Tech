<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceRequest>
 */
class ServiceRequestFactory extends Factory
{
    protected $model = ServiceRequest::class;

    public function definition(): array
    {
        return [
            'customer_name' => $this->faker->name(),
            'customer_phone' => $this->faker->phoneNumber(),
            'customer_email' => $this->faker->optional()->safeEmail(),
            'service_id' => Service::factory(),
            'device_description' => $this->faker->sentence(),
            'problem_description' => $this->faker->paragraph(),
            'status' => 'pending',
            'estimated_cost' => $this->faker->optional()->randomFloat(2, 50, 500),
            'final_cost' => null,
            'notes' => null,
            'completed_at' => null,
        ];
    }
}

<?php

namespace Tests\Feature\Website;

use App\Models\Product;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_returns_200_and_lists_featured_services(): void
    {
        Service::factory()->create(['name' => 'Featured A', 'is_active' => true, 'is_featured' => true]);
        Service::factory()->create(['name' => 'Featured B', 'is_active' => true, 'is_featured' => true]);
        Service::factory()->create(['name' => 'Not Featured', 'is_active' => true, 'is_featured' => false]);
        Product::factory()->count(10)->create(['is_active' => true, 'stock_quantity' => 3]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Featured A');
        $response->assertSee('Featured B');
        $response->assertDontSee('Not Featured');
    }
}

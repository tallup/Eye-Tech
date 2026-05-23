<?php

namespace Tests\Unit;

use App\Exceptions\InsufficientStockException;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Services\CheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CheckoutServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_successful_checkout_creates_sale_and_decrements_stock(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $product = Product::factory()->create([
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
            'stock_quantity' => 10,
            'selling_price' => 25.00,
        ]);

        $service = app(CheckoutService::class);
        $sale = $service->checkout(
            user: $cashier,
            items: [['product_id' => $product->id, 'quantity' => 3]],
            customer: ['name' => 'Test', 'phone' => null, 'email' => null],
            paymentMethod: 'cash',
        );

        $this->assertNotNull($sale->sale_number);
        $this->assertEquals(75.00, (float) $sale->total_amount);
        $this->assertCount(1, $sale->items);
        $this->assertEquals(7, $product->fresh()->stock_quantity);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'movement_type' => 'sale',
            'quantity' => 3,
            'previous_quantity' => 10,
            'new_quantity' => 7,
        ]);
    }

    public function test_throws_insufficient_stock_when_quantity_exceeds_available(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $product = Product::factory()->create([
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
            'stock_quantity' => 2,
        ]);

        $service = app(CheckoutService::class);

        try {
            $service->checkout(
                user: $cashier,
                items: [['product_id' => $product->id, 'quantity' => 5]],
                customer: ['name' => null, 'phone' => null, 'email' => null],
                paymentMethod: 'cash',
            );
            $this->fail('Expected InsufficientStockException');
        } catch (InsufficientStockException $e) {
            // Stock should NOT be decremented on failure (transaction rolled back)
            $this->assertEquals(2, $product->fresh()->stock_quantity);
            $this->assertEquals(0, \App\Models\Sales::count(), 'no sale created on failure');
        }
    }
}

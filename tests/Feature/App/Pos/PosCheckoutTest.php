<?php

namespace Tests\Feature\App\Pos;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sales;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PosCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_checkout_creates_sale_and_redirects_to_receipt(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);

        $product = Product::factory()->create([
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
            'stock_quantity' => 5,
            'selling_price' => 10.00,
        ]);

        $response = $this->actingAs($cashier)->post('/app/pos/checkout', [
            'customer_name' => 'Walk-in',
            'payment_method' => 'cash',
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
        ]);

        $sale = Sales::first();
        $this->assertNotNull($sale, 'Sale should be created');
        $response->assertRedirect('/app/sales/'.$sale->id);
        $response->assertSessionHas('print', true);

        $this->assertEquals(20.00, (float) $sale->total_amount);
        $this->assertEquals(3, $product->fresh()->stock_quantity);
    }

    public function test_checkout_rejects_insufficient_stock(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);

        $product = Product::factory()->create([
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
            'stock_quantity' => 1,
        ]);

        $this->actingAs($cashier)
            ->from('/app/pos')
            ->post('/app/pos/checkout', [
                'payment_method' => 'cash',
                'items' => [['product_id' => $product->id, 'quantity' => 5]],
            ])
            ->assertRedirect('/app/pos')
            ->assertSessionHas('error');

        $this->assertEquals(1, $product->fresh()->stock_quantity, 'stock unchanged on failure');
        $this->assertEquals(0, Sales::count(), 'no sale created');
    }

    public function test_checkout_validates_payload(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);

        $this->actingAs($cashier)
            ->from('/app/pos')
            ->post('/app/pos/checkout', [
                'payment_method' => 'cash',
                'items' => [],
            ])
            ->assertSessionHasErrors('items');
    }
}

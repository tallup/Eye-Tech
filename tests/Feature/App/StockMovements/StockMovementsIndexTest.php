<?php

namespace Tests\Feature\App\StockMovements;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StockMovementsIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_admin_can_view_stock_movements(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $product = Product::factory()->create([
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
        ]);

        StockMovement::create([
            'product_id' => $product->id,
            'movement_type' => 'in',
            'quantity' => 10,
            'previous_quantity' => 0,
            'new_quantity' => 10,
            'reference_type' => 'manual',
            'reference_id' => null,
            'user_id' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->withoutVite()
            ->get('/app/stock-movements')
            ->assertOk()
            ->assertInertia(fn ($p) => $p->component('StockMovements/Index')->has('movements.data', 1));
    }

    public function test_cashier_blocked_from_stock_movements(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);

        $this->actingAs($cashier)
            ->get('/app/stock-movements')
            ->assertForbidden();
    }

    public function test_filter_by_type(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $product = Product::factory()->create([
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
        ]);

        foreach (['in', 'out', 'sale'] as $type) {
            StockMovement::create([
                'product_id' => $product->id,
                'movement_type' => $type,
                'quantity' => 1,
                'previous_quantity' => 0,
                'new_quantity' => 1,
                'reference_type' => 'test',
                'reference_id' => null,
                'user_id' => $admin->id,
            ]);
        }

        $this->actingAs($admin)
            ->withoutVite()
            ->get('/app/stock-movements?type=sale')
            ->assertOk()
            ->assertInertia(fn ($p) => $p->has('movements.data', 1));
    }
}

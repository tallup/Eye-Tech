<?php

namespace Tests\Feature\App\Pos;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PosIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_pos_loads_active_products_and_categories(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);

        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();

        $activeProduct = Product::factory()->create([
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'is_active' => true,
            'stock_quantity' => 5,
        ]);
        Product::factory()->create([
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'is_active' => false,
        ]);

        $this->actingAs($cashier)
            ->withoutVite()
            ->get('/app/pos')
            ->assertOk()
            ->assertInertia(fn ($p) => $p
                ->component('POS/Index')
                ->has('products.data', 1)
                ->where('products.data.0.id', $activeProduct->id)
                ->has('categories.data', 1)
            );
    }
}

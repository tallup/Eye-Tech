<?php

namespace Tests\Feature\App\Products;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductsCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_admin_can_view_products_index(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        Product::factory()->count(3)->create([
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
        ]);

        $this->actingAs($admin)
            ->withoutVite()
            ->get('/app/products')
            ->assertOk()
            ->assertInertia(fn ($p) => $p->component('Products/Index')->has('products.data', 3));
    }

    public function test_cashier_blocked_from_products(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);

        $this->actingAs($cashier)->get('/app/products')->assertForbidden();
    }

    public function test_admin_can_view_create_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        Category::factory()->count(2)->create();
        Supplier::factory()->count(2)->create();

        $this->actingAs($admin)
            ->withoutVite()
            ->get('/app/products/create')
            ->assertOk()
            ->assertInertia(fn ($p) => $p
                ->component('Products/Edit')
                ->has('categories', 2)
                ->has('suppliers', 2)
            );
    }

    public function test_admin_can_create_product(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();

        $this->actingAs($admin)
            ->post('/app/products', [
                'name' => 'Test Product',
                'sku' => 'TEST-001',
                'category_id' => $category->id,
                'supplier_id' => $supplier->id,
                'cost_price' => 50.00,
                'selling_price' => 100.00,
                'stock_quantity' => 10,
                'min_stock_level' => 2,
                'is_active' => true,
            ])
            ->assertRedirect('/app/products');

        $this->assertDatabaseHas('products', ['sku' => 'TEST-001', 'selling_price' => 100.00]);
    }

    public function test_admin_can_update_product(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $product = Product::factory()->create([
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
            'name' => 'Old Name',
        ]);

        $this->actingAs($admin)
            ->put("/app/products/{$product->id}", [
                'name' => 'New Name',
                'sku' => $product->sku,
                'category_id' => $product->category_id,
                'supplier_id' => $product->supplier_id,
                'cost_price' => $product->cost_price,
                'selling_price' => $product->selling_price,
                'stock_quantity' => $product->stock_quantity,
                'min_stock_level' => $product->min_stock_level,
                'is_active' => true,
            ])
            ->assertRedirect('/app/products');

        $this->assertEquals('New Name', $product->fresh()->name);
    }

    public function test_admin_can_delete_product(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $product = Product::factory()->create([
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
        ]);

        $this->actingAs($admin)
            ->delete("/app/products/{$product->id}")
            ->assertRedirect('/app/products');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_validation_rejects_duplicate_sku(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        Product::factory()->create([
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
            'sku' => 'DUP-001',
        ]);

        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();

        $this->actingAs($admin)
            ->from('/app/products/create')
            ->post('/app/products', [
                'name' => 'Duplicate',
                'sku' => 'DUP-001',
                'category_id' => $category->id,
                'supplier_id' => $supplier->id,
                'cost_price' => 10,
                'selling_price' => 20,
                'stock_quantity' => 0,
                'min_stock_level' => 0,
                'is_active' => true,
            ])
            ->assertSessionHasErrors('sku');
    }

    public function test_image_upload_stores_file(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();

        $this->actingAs($admin)
            ->post('/app/products', [
                'name' => 'With Image',
                'sku' => 'IMG-001',
                'category_id' => $category->id,
                'supplier_id' => $supplier->id,
                'cost_price' => 10,
                'selling_price' => 20,
                'stock_quantity' => 5,
                'min_stock_level' => 1,
                'is_active' => true,
                'image' => UploadedFile::fake()->image('product.jpg'),
            ])
            ->assertRedirect('/app/products');

        $product = Product::where('sku', 'IMG-001')->first();
        $this->assertNotNull($product->image);
        Storage::disk('public')->assertExists($product->image);
    }
}

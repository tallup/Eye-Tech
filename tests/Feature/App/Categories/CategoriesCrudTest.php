<?php

namespace Tests\Feature\App\Categories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CategoriesCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_admin_can_view_categories_index(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);
        Category::factory()->count(3)->create();

        $this->actingAs($admin)
            ->withoutVite()
            ->get('/app/categories')
            ->assertOk()
            ->assertInertia(fn ($p) => $p->component('Categories/Index')->has('categories.data', 3));
    }

    public function test_cashier_blocked_from_categories(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);

        $this->actingAs($cashier)->get('/app/categories')->assertForbidden();
    }

    public function test_admin_can_create_category_with_auto_slug(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $this->actingAs($admin)
            ->post('/app/categories', [
                'name' => 'Sunglasses',
                'description' => 'Eye protection',
            ])
            ->assertRedirect('/app/categories');

        $this->assertDatabaseHas('categories', ['name' => 'Sunglasses', 'slug' => 'sunglasses']);
    }

    public function test_admin_can_update_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $category = Category::factory()->create(['name' => 'Old', 'slug' => 'old']);

        $this->actingAs($admin)
            ->put("/app/categories/{$category->id}", [
                'name' => 'New name',
                'slug' => 'new-name',
            ])
            ->assertRedirect('/app/categories');

        $this->assertEquals('New name', $category->fresh()->name);
        $this->assertEquals('new-name', $category->fresh()->slug);
    }

    public function test_admin_can_delete_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $category = Category::factory()->create();

        $this->actingAs($admin)
            ->delete("/app/categories/{$category->id}")
            ->assertRedirect('/app/categories');

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}

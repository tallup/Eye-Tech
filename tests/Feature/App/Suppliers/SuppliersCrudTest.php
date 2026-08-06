<?php

namespace Tests\Feature\App\Suppliers;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SuppliersCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_admin_can_view_suppliers_index(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        Supplier::factory()->count(3)->create();

        $this->actingAs($admin)
            ->withoutVite()
            ->get('/app/suppliers')
            ->assertOk()
            ->assertInertia(fn ($p) => $p->component('Suppliers/Index')->has('suppliers.data', 3));
    }

    public function test_cashier_blocked_from_suppliers(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);

        $this->actingAs($cashier)
            ->get('/app/suppliers')
            ->assertForbidden();
    }

    public function test_admin_can_create_supplier(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $this->actingAs($admin)
            ->post('/app/suppliers', [
                'name' => 'Acme Inc',
                'email' => 'acme@example.test',
                'phone' => '0001112222',
                'address' => '123 Main St',
            ])
            ->assertRedirect('/app/suppliers');

        $this->assertDatabaseHas('suppliers', ['name' => 'Acme Inc']);
    }

    public function test_admin_can_update_supplier(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $supplier = Supplier::factory()->create(['name' => 'Old Name']);

        $this->actingAs($admin)
            ->put("/app/suppliers/{$supplier->id}", [
                'name' => 'New Name',
                'email' => $supplier->email,
                'phone' => $supplier->phone,
                'address' => $supplier->address,
            ])
            ->assertRedirect('/app/suppliers');

        $this->assertEquals('New Name', $supplier->fresh()->name);
    }

    public function test_admin_can_delete_supplier(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $supplier = Supplier::factory()->create();

        $this->actingAs($admin)
            ->delete("/app/suppliers/{$supplier->id}")
            ->assertRedirect('/app/suppliers');

        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }
}

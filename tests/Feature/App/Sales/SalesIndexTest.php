<?php

namespace Tests\Feature\App\Sales;

use App\Models\Sales;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SalesIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_admin_sees_all_sales(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $other = User::factory()->create(['role' => 'cashier']);
        Sales::factory()->count(3)->create(['user_id' => $other->id]);

        $this->actingAs($admin)
            ->withoutVite()
            ->get('/app/sales')
            ->assertOk()
            ->assertInertia(fn ($p) => $p
                ->component('Sales/Index')
                ->has('sales.data', 3));
    }

    public function test_cashier_sees_only_own_sales(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);

        $other = User::factory()->create(['role' => 'cashier']);
        Sales::factory()->count(2)->create(['user_id' => $cashier->id]);
        Sales::factory()->count(3)->create(['user_id' => $other->id]);

        $this->actingAs($cashier)
            ->withoutVite()
            ->get('/app/sales')
            ->assertOk()
            ->assertInertia(fn ($p) => $p
                ->component('Sales/Index')
                ->has('sales.data', 2));
    }
}

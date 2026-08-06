<?php

namespace Tests\Feature\App\Sales;

use App\Models\Sales;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SalesAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_cashier_cannot_view_another_cashiers_sale(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);

        $other = User::factory()->create(['role' => 'cashier']);
        $sale = Sales::factory()->create(['user_id' => $other->id]);

        $this->actingAs($cashier)
            ->get("/app/sales/{$sale->id}")
            ->assertForbidden();
    }

    public function test_admin_can_view_any_sale(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $other = User::factory()->create(['role' => 'cashier']);
        $sale = Sales::factory()->create(['user_id' => $other->id]);

        $this->actingAs($admin)
            ->withoutVite()
            ->get("/app/sales/{$sale->id}")
            ->assertOk();
    }
}

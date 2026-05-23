<?php

namespace Tests\Feature\App\Sales;

use App\Models\Sales;
use App\Models\SalesItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SalesShowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_cashier_can_view_own_sale_with_items(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);

        $sale = Sales::factory()->create(['user_id' => $cashier->id]);
        SalesItem::factory()->count(2)->create(['sale_id' => $sale->id]);

        $this->actingAs($cashier)
            ->withoutVite()
            ->get("/app/sales/{$sale->id}")
            ->assertOk()
            ->assertInertia(fn ($p) => $p
                ->component('Sales/Show')
                ->has('sale.data.items', 2)
            );
    }
}

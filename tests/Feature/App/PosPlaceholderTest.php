<?php

namespace Tests\Feature\App;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PosPlaceholderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_admin_can_reach_pos(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $this->actingAs($admin)
            ->withoutVite()
            ->get('/app/pos')
            ->assertOk()
            ->assertInertia(fn ($p) => $p->component('POS/Index'));
    }

    public function test_cashier_can_reach_pos(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);

        $this->actingAs($cashier)
            ->withoutVite()
            ->get('/app/pos')
            ->assertOk()
            ->assertInertia(fn ($p) => $p->component('POS/Index'));
    }
}

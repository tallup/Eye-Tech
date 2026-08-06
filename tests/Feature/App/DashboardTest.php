<?php

namespace Tests\Feature\App;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_admin_sees_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $this->actingAs($admin)
            ->withoutVite()
            ->get('/app/dashboard')
            ->assertOk()
            ->assertInertia(fn ($p) => $p->component('Dashboard'));
    }

    public function test_cashier_blocked_from_dashboard(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);

        $this->actingAs($cashier)
            ->get('/app/dashboard')
            ->assertForbidden();
    }

    public function test_guest_redirected_to_login(): void
    {
        $this->get('/app/dashboard')
            ->assertRedirect('/login');
    }
}

<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_login_page_renders_inertia_component(): void
    {
        $this->withoutVite()
            ->get('/login')
            ->assertOk()
            ->assertInertia(fn ($p) => $p->component('Auth/Login'));
    }

    public function test_admin_logs_in_and_is_redirected_to_dashboard(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.test',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);
        $admin->syncRoles(['admin']);

        $this->post('/login', [
            'email' => 'admin@example.test',
            'password' => 'secret123',
        ])
            ->assertRedirect('/app/dashboard');

        $this->assertAuthenticatedAs($admin);
    }

    public function test_cashier_logs_in_and_is_redirected_to_pos(): void
    {
        $cashier = User::factory()->create([
            'email' => 'cashier@example.test',
            'password' => Hash::make('secret123'),
            'role' => 'cashier',
        ]);
        $cashier->syncRoles(['cashier']);

        $this->post('/login', [
            'email' => 'cashier@example.test',
            'password' => 'secret123',
        ])
            ->assertRedirect('/app/pos');
    }

    public function test_invalid_credentials_return_with_error(): void
    {
        User::factory()->create([
            'email' => 'foo@bar.test',
            'password' => Hash::make('right-password'),
        ]);

        $this->from('/login')
            ->post('/login', ['email' => 'foo@bar.test', 'password' => 'wrong'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}

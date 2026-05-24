<?php

namespace Tests\Feature\App\Users;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UsersCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_admin_can_view_users_index(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);
        User::factory()->count(2)->create();

        $this->actingAs($admin)
            ->withoutVite()
            ->get('/app/users')
            ->assertOk()
            ->assertInertia(fn ($p) => $p->component('Users/Index')->has('users.data', 3));
    }

    public function test_cashier_blocked_from_users(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);

        $this->actingAs($cashier)
            ->get('/app/users')
            ->assertForbidden();
    }

    public function test_admin_can_create_cashier_with_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $this->actingAs($admin)
            ->post('/app/users', [
                'name' => 'New Cashier',
                'email' => 'cashier@eyetech.test',
                'role' => 'cashier',
                'password' => 'secret123',
                'password_confirmation' => 'secret123',
            ])
            ->assertRedirect('/app/users');

        $u = User::where('email', 'cashier@eyetech.test')->firstOrFail();
        $this->assertEquals('cashier', $u->role);
        $this->assertTrue($u->hasRole('cashier'));
    }

    public function test_admin_can_update_user_without_changing_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $target = User::factory()->create(['role' => 'cashier', 'name' => 'Old']);
        $target->syncRoles(['cashier']);
        $oldHash = $target->password;

        $this->actingAs($admin)
            ->put("/app/users/{$target->id}", [
                'name' => 'New',
                'email' => $target->email,
                'role' => 'cashier',
            ])
            ->assertRedirect('/app/users');

        $fresh = $target->fresh();
        $this->assertEquals('New', $fresh->name);
        $this->assertEquals($oldHash, $fresh->password);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $this->actingAs($admin)
            ->delete("/app/users/{$admin->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_can_delete_another_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);
        $target = User::factory()->create(['role' => 'cashier']);

        $this->actingAs($admin)
            ->delete("/app/users/{$target->id}")
            ->assertRedirect('/app/users');

        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }
}

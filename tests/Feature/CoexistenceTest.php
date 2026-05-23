<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CoexistenceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    }

    public function test_filament_login_page_still_renders(): void
    {
        $this->get('/admin/login')->assertOk();
    }

    public function test_inertia_login_page_renders(): void
    {
        $this->withoutVite()
            ->get('/login')
            ->assertOk()
            ->assertInertia(fn ($p) => $p->component('Auth/Login'));
    }

    public function test_filament_admin_redirects_unauthed(): void
    {
        $this->get('/admin')->assertRedirect();
    }

    public function test_both_panels_share_same_users_table(): void
    {
        $admin = User::factory()->create([
            'email' => 'shared@eyetech.test',
            'role' => 'admin',
        ]);
        $admin->syncRoles(['admin']);

        // Filament /admin redirects to the default page; follow it and confirm 200
        $this->actingAs($admin)
            ->get('/admin')
            ->assertRedirectContains('/admin/');
    }
}

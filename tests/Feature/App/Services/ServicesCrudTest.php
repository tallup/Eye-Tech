<?php

namespace Tests\Feature\App\Services;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ServicesCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_admin_can_view_services_index(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        Service::factory()->count(3)->create();

        $this->actingAs($admin)
            ->withoutVite()
            ->get('/app/services')
            ->assertOk()
            ->assertInertia(fn ($p) => $p->component('Services/Index')->has('services.data', 3));
    }

    public function test_cashier_blocked_from_services(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);

        $this->actingAs($cashier)
            ->get('/app/services')
            ->assertForbidden();
    }

    public function test_admin_can_create_service(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $this->actingAs($admin)
            ->post('/app/services', [
                'name' => 'Screen Replacement',
                'description' => 'Replace cracked screen',
                'price' => 250.00,
                'estimated_duration' => 60,
                'category' => 'repair',
                'is_active' => true,
                'is_featured' => true,
            ])
            ->assertRedirect('/app/services');

        $this->assertDatabaseHas('services', [
            'name' => 'Screen Replacement',
            'slug' => 'screen-replacement',
            'is_featured' => true,
        ]);
    }

    public function test_admin_can_update_service(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $service = Service::factory()->create(['name' => 'Old Name']);

        $this->actingAs($admin)
            ->put("/app/services/{$service->id}", [
                'name' => 'New Name',
                'slug' => $service->slug,
                'description' => $service->description,
                'price' => $service->price,
                'is_active' => true,
                'is_featured' => false,
            ])
            ->assertRedirect('/app/services');

        $this->assertEquals('New Name', $service->fresh()->name);
    }

    public function test_admin_can_delete_service(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $service = Service::factory()->create();

        $this->actingAs($admin)
            ->delete("/app/services/{$service->id}")
            ->assertRedirect('/app/services');

        $this->assertDatabaseMissing('services', ['id' => $service->id]);
    }
}

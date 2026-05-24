<?php

namespace Tests\Feature\App\ServiceRequests;

use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ServiceRequestsCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_admin_can_view_service_requests_index(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        ServiceRequest::factory()->count(3)->create();

        $this->actingAs($admin)
            ->withoutVite()
            ->get('/app/service-requests')
            ->assertOk()
            ->assertInertia(fn ($p) => $p
                ->component('ServiceRequests/Index')
                ->has('serviceRequests.data', 3));
    }

    public function test_cashier_can_view_service_requests_but_cannot_modify(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);

        $sr = ServiceRequest::factory()->create();

        $this->actingAs($cashier)
            ->withoutVite()
            ->get('/app/service-requests')
            ->assertOk();

        $this->actingAs($cashier)
            ->withoutVite()
            ->get("/app/service-requests/{$sr->id}")
            ->assertOk();

        $this->actingAs($cashier)
            ->delete("/app/service-requests/{$sr->id}")
            ->assertForbidden();
    }

    public function test_admin_can_create_service_request(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $service = Service::factory()->create();

        $this->actingAs($admin)
            ->post('/app/service-requests', [
                'customer_name' => 'Adama Jallow',
                'customer_phone' => '7700000',
                'service_id' => $service->id,
                'device_description' => 'iPhone 12',
                'problem_description' => 'Cracked screen',
            ])
            ->assertRedirect('/app/service-requests');

        $this->assertDatabaseHas('service_requests', [
            'customer_name' => 'Adama Jallow',
            'status' => 'pending',
        ]);
    }

    public function test_status_transition_to_completed_sets_completed_at(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $sr = ServiceRequest::factory()->create([
            'status' => 'in_progress',
            'completed_at' => null,
        ]);

        $payload = [
            'customer_name' => $sr->customer_name,
            'customer_phone' => $sr->customer_phone,
            'customer_email' => $sr->customer_email,
            'service_id' => $sr->service_id,
            'device_description' => $sr->device_description,
            'problem_description' => $sr->problem_description,
            'status' => 'completed',
        ];

        $this->actingAs($admin)
            ->put("/app/service-requests/{$sr->id}", $payload)
            ->assertRedirect("/app/service-requests/{$sr->id}");

        $fresh = $sr->fresh();
        $this->assertEquals('completed', $fresh->status);
        $this->assertNotNull($fresh->completed_at);
    }

    public function test_admin_can_delete_service_request(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $sr = ServiceRequest::factory()->create();

        $this->actingAs($admin)
            ->delete("/app/service-requests/{$sr->id}")
            ->assertRedirect('/app/service-requests');

        $this->assertDatabaseMissing('service_requests', ['id' => $sr->id]);
    }
}

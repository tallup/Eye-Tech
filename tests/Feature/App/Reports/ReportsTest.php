<?php

namespace Tests\Feature\App\Reports;

use App\Models\Sales;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_reports_index_renders_for_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        Sales::factory()->create([
            'user_id' => $admin->id,
            'status' => 'completed',
            'total_amount' => 100,
            'created_at' => now(),
        ]);

        $this->actingAs($admin)
            ->withoutVite()
            ->get('/app/reports')
            ->assertOk()
            ->assertInertia(fn ($p) => $p
                ->component('Reports/Index')
                ->where('report.totals.sales_count', 1)
                ->where('report.totals.revenue', 100.0)
                ->has('report.daily_chart', 7)
                ->has('report.top_staff')
                ->has('report.sales_by_status')
                ->has('report.inventory')
            );
    }

    public function test_reports_index_blocked_for_cashier(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);

        $this->actingAs($cashier)
            ->get('/app/reports')
            ->assertForbidden();
    }

    public function test_custom_reports_renders_for_admin_with_30_day_chart(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $this->actingAs($admin)
            ->withoutVite()
            ->get('/app/reports/custom')
            ->assertOk()
            ->assertInertia(fn ($p) => $p
                ->component('Reports/Custom')
                ->has('report.daily_chart', 30)
                ->has('report.service_requests')
                ->has('report.inventory')
            );
    }

    public function test_custom_reports_blocked_for_cashier(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);

        $this->actingAs($cashier)
            ->get('/app/reports/custom')
            ->assertForbidden();
    }
}

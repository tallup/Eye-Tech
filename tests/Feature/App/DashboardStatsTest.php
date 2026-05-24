<?php

namespace Tests\Feature\App;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sales;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardStatsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_dashboard_returns_stats_for_today(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        Sales::factory()->create([
            'user_id' => $admin->id,
            'status' => 'completed',
            'total_amount' => 100,
            'created_at' => now(),
        ]);

        Product::factory()->count(2)->create([
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
            'is_active' => true,
            'stock_quantity' => 0,
            'min_stock_level' => 5,
        ]);

        $this->actingAs($admin)
            ->withoutVite()
            ->get('/app/dashboard')
            ->assertOk()
            ->assertInertia(fn ($p) => $p
                ->component('Dashboard')
                ->where('stats.today_sales_total', 100.0)
                ->where('stats.today_sales_count', 1)
                ->where('stats.low_stock_count', 2)
            );
    }

    public function test_dashboard_includes_seven_day_sales_chart(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $this->actingAs($admin)
            ->withoutVite()
            ->get('/app/dashboard')
            ->assertInertia(fn ($p) => $p->has('sales_chart', 7));
    }
}

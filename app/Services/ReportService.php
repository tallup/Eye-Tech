<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sales;
use App\Models\ServiceRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function payload(int $chartDays, int $topLimit): array
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $totalSales = (int) Sales::count();
        $totalRevenue = (float) Sales::sum('total_amount');
        $todaySales = (int) Sales::whereDate('created_at', $today)->count();
        $todayRevenue = (float) Sales::whereDate('created_at', $today)->sum('total_amount');
        $weekSales = (int) Sales::where('created_at', '>=', $thisWeek)->count();
        $weekRevenue = (float) Sales::where('created_at', '>=', $thisWeek)->sum('total_amount');
        $monthSales = (int) Sales::where('created_at', '>=', $thisMonth)->count();
        $monthRevenue = (float) Sales::where('created_at', '>=', $thisMonth)->sum('total_amount');
        $lastMonthSales = (int) Sales::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->count();
        $lastMonthRevenue = (float) Sales::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->sum('total_amount');

        $salesGrowth = $lastMonthSales > 0 ? (($monthSales - $lastMonthSales) / $lastMonthSales) * 100 : 0.0;
        $revenueGrowth = $lastMonthRevenue > 0 ? (($monthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0.0;

        $topProducts = DB::table('sales_items')
            ->join('products', 'sales_items.product_id', '=', 'products.id')
            ->select(
                'products.name',
                'products.sku',
                DB::raw('SUM(sales_items.quantity) as total_quantity'),
                DB::raw('SUM(sales_items.total_price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderBy('total_quantity', 'desc')
            ->limit($topLimit)
            ->get()
            ->map(fn ($r) => [
                'name' => $r->name,
                'sku' => $r->sku,
                'quantity' => (int) $r->total_quantity,
                'revenue' => (float) $r->total_revenue,
            ])
            ->toArray();

        $salesByStatus = Sales::select(
            'status',
            DB::raw('count(*) as count'),
            DB::raw('sum(total_amount) as revenue')
        )
            ->groupBy('status')
            ->get()
            ->map(fn ($r) => [
                'status' => $r->status,
                'count' => (int) $r->count,
                'revenue' => (float) $r->revenue,
            ])
            ->toArray();

        $salesByPayment = Sales::select(
            'payment_method',
            DB::raw('count(*) as count'),
            DB::raw('sum(total_amount) as revenue')
        )
            ->groupBy('payment_method')
            ->get()
            ->map(fn ($r) => [
                'payment_method' => $r->payment_method,
                'count' => (int) $r->count,
                'revenue' => (float) $r->revenue,
            ])
            ->toArray();

        $dailySales = Sales::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as sales_count'),
            DB::raw('sum(total_amount) as revenue')
        )
            ->where('created_at', '>=', Carbon::now()->subDays($chartDays))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dailyChart = [];
        for ($i = $chartDays - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayData = $dailySales->firstWhere('date', $date->format('Y-m-d'));
            $dailyChart[] = [
                'date' => $date->format('M j'),
                'sales_count' => (int) ($dayData->sales_count ?? 0),
                'revenue' => (float) ($dayData->revenue ?? 0),
            ];
        }

        $topStaff = Sales::join('users', 'sales.user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.name',
                DB::raw('count(*) as sales_count'),
                DB::raw('sum(sales.total_amount) as total_revenue')
            )
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_revenue', 'desc')
            ->limit($topLimit)
            ->get()
            ->map(fn ($r) => [
                'id' => (int) $r->id,
                'name' => $r->name,
                'sales_count' => (int) $r->sales_count,
                'revenue' => (float) $r->total_revenue,
            ])
            ->toArray();

        $totalServiceRequests = (int) ServiceRequest::count();
        $pendingRequests = (int) ServiceRequest::where('status', 'pending')->count();
        $completedRequests = (int) ServiceRequest::where('status', 'completed')->count();
        $inProgressRequests = (int) ServiceRequest::where('status', 'in_progress')->count();

        $totalProducts = (int) Product::count();
        $activeProducts = (int) Product::where('is_active', true)->count();
        $lowStockProducts = (int) Product::whereColumn('stock_quantity', '<=', 'min_stock_level')->count();
        $outOfStockProducts = (int) Product::where('stock_quantity', 0)->count();

        $averageOrderValue = $totalSales > 0 ? $totalRevenue / $totalSales : 0.0;
        $conversionRate = $totalServiceRequests > 0 ? ($totalSales / $totalServiceRequests) * 100 : 0.0;

        return [
            'totals' => [
                'sales_count' => $totalSales,
                'revenue' => $totalRevenue,
                'today_sales' => $todaySales,
                'today_revenue' => $todayRevenue,
                'week_sales' => $weekSales,
                'week_revenue' => $weekRevenue,
                'month_sales' => $monthSales,
                'month_revenue' => $monthRevenue,
                'avg_order_value' => $averageOrderValue,
            ],
            'growth' => [
                'sales_pct' => $salesGrowth,
                'revenue_pct' => $revenueGrowth,
            ],
            'daily_chart' => $dailyChart,
            'top_products' => $topProducts,
            'top_staff' => $topStaff,
            'sales_by_status' => $salesByStatus,
            'sales_by_payment' => $salesByPayment,
            'service_requests' => [
                'total' => $totalServiceRequests,
                'pending' => $pendingRequests,
                'in_progress' => $inProgressRequests,
                'completed' => $completedRequests,
                'conversion_rate' => $conversionRate,
            ],
            'inventory' => [
                'total' => $totalProducts,
                'active' => $activeProducts,
                'low_stock' => $lowStockProducts,
                'out_of_stock' => $outOfStockProducts,
            ],
        ];
    }
}

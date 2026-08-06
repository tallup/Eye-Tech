<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sales;
use App\Models\SalesItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $today = today();
        $yesterday = today()->subDay();
        $weekStart = today()->startOfWeek();
        $monthStart = today()->startOfMonth();
        $prevWeekStart = today()->subWeek()->startOfWeek();
        $prevWeekEnd = today()->subWeek()->endOfWeek();

        // Sales metrics
        $todaySalesTotal = (float) Sales::whereDate('created_at', $today)
            ->where('status', 'completed')->sum('total_amount');
        $todaySalesCount = Sales::whereDate('created_at', $today)
            ->where('status', 'completed')->count();
        $yesterdaySalesTotal = (float) Sales::whereDate('created_at', $yesterday)
            ->where('status', 'completed')->sum('total_amount');
        $weekSalesTotal = (float) Sales::whereBetween('created_at', [$weekStart, today()->endOfDay()])
            ->where('status', 'completed')->sum('total_amount');
        $weekSalesCount = Sales::whereBetween('created_at', [$weekStart, today()->endOfDay()])
            ->where('status', 'completed')->count();
        $prevWeekSalesTotal = (float) Sales::whereBetween('created_at', [$prevWeekStart, $prevWeekEnd])
            ->where('status', 'completed')->sum('total_amount');
        $monthSalesTotal = (float) Sales::whereBetween('created_at', [$monthStart, today()->endOfDay()])
            ->where('status', 'completed')->sum('total_amount');
        $monthSalesCount = Sales::whereBetween('created_at', [$monthStart, today()->endOfDay()])
            ->where('status', 'completed')->count();

        $deltaToday = $yesterdaySalesTotal > 0
            ? round((($todaySalesTotal - $yesterdaySalesTotal) / $yesterdaySalesTotal) * 100, 1)
            : null;
        $deltaWeek = $prevWeekSalesTotal > 0
            ? round((($weekSalesTotal - $prevWeekSalesTotal) / $prevWeekSalesTotal) * 100, 1)
            : null;

        // Inventory metrics
        $totalProducts = Product::where('is_active', true)->count();
        $totalStockUnits = (int) Product::where('is_active', true)->sum('stock_quantity');
        $inventoryValueCost = (float) Product::where('is_active', true)
            ->select(DB::raw('SUM(stock_quantity * cost_price) as v'))->value('v');
        $inventoryValueRetail = (float) Product::where('is_active', true)
            ->select(DB::raw('SUM(stock_quantity * selling_price) as v'))->value('v');
        $lowStockCount = Product::where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'min_stock_level')
            ->where('stock_quantity', '>', 0)
            ->count();
        $outOfStockCount = Product::where('is_active', true)
            ->where('stock_quantity', 0)->count();

        // 14-day sales chart
        $salesChart = collect(range(13, 0))->map(function (int $daysAgo) {
            $date = today()->subDays($daysAgo);

            return [
                'date' => $date->format('M d'),
                'total' => (float) Sales::whereDate('created_at', $date)
                    ->where('status', 'completed')->sum('total_amount'),
                'count' => Sales::whereDate('created_at', $date)
                    ->where('status', 'completed')->count(),
            ];
        })->values();

        // Top 10 products this month by units sold
        $topProducts = SalesItem::query()
            ->join('sales', 'sales.id', '=', 'sales_items.sale_id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.created_at', [$monthStart, today()->endOfDay()])
            ->select(
                'sales_items.product_id',
                'sales_items.item_name as name',
                'sales_items.item_sku as sku',
                DB::raw('SUM(sales_items.quantity) as units_sold'),
                DB::raw('SUM(sales_items.total_price) as revenue'),
            )
            ->groupBy('sales_items.product_id', 'sales_items.item_name', 'sales_items.item_sku')
            ->orderByDesc('units_sold')
            ->limit(10)
            ->get()
            ->map(fn ($r) => [
                'product_id' => $r->product_id,
                'name' => $r->name,
                'sku' => $r->sku,
                'units_sold' => (int) $r->units_sold,
                'revenue' => (float) $r->revenue,
            ]);

        // Top categories by revenue this month
        $topCategories = SalesItem::query()
            ->join('sales', 'sales.id', '=', 'sales_items.sale_id')
            ->join('products', 'products.id', '=', 'sales_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.created_at', [$monthStart, today()->endOfDay()])
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('SUM(sales_items.total_price) as revenue'),
                DB::raw('SUM(sales_items.quantity) as units'),
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'revenue' => (float) $r->revenue,
                'units' => (int) $r->units,
            ]);

        // Payment method breakdown this month
        $paymentBreakdown = Sales::query()
            ->whereBetween('created_at', [$monthStart, today()->endOfDay()])
            ->where('status', 'completed')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('payment_method')
            ->get()
            ->map(fn ($r) => [
                'method' => $r->payment_method,
                'count' => (int) $r->count,
                'total' => (float) $r->total,
            ]);

        // Recent sales
        $recentSales = Sales::with('user')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'sale_number' => $s->sale_number,
                'customer_name' => $s->customer_name,
                'total_amount' => (float) $s->total_amount,
                'payment_method' => $s->payment_method,
                'status' => $s->status,
                'cashier' => $s->user?->name,
                'created_at' => $s->created_at?->toIso8601String(),
            ]);

        // Recent stock movements
        $recentMovements = StockMovement::with(['product', 'user'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'product_name' => $m->product?->name,
                'product_sku' => $m->product?->sku,
                'movement_type' => $m->movement_type,
                'quantity' => $m->quantity,
                'previous_quantity' => $m->previous_quantity,
                'new_quantity' => $m->new_quantity,
                'user_name' => $m->user?->name,
                'created_at' => $m->created_at?->toIso8601String(),
            ]);

        // Low stock list
        $lowStockProducts = Product::with('category')
            ->where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'min_stock_level')
            ->orderBy('stock_quantity')
            ->limit(8)
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
                'stock_quantity' => $p->stock_quantity,
                'min_stock_level' => $p->min_stock_level,
                'category' => $p->category?->name,
            ]);

        return Inertia::render('Dashboard', [
            'stats' => [
                'today_sales_total' => $todaySalesTotal,
                'today_sales_count' => $todaySalesCount,
                'yesterday_sales_total' => $yesterdaySalesTotal,
                'delta_today_pct' => $deltaToday,
                'week_sales_total' => $weekSalesTotal,
                'week_sales_count' => $weekSalesCount,
                'delta_week_pct' => $deltaWeek,
                'month_sales_total' => $monthSalesTotal,
                'month_sales_count' => $monthSalesCount,
                'total_products' => $totalProducts,
                'total_stock_units' => $totalStockUnits,
                'inventory_value_cost' => $inventoryValueCost,
                'inventory_value_retail' => $inventoryValueRetail,
                'low_stock_count' => $lowStockCount,
                'out_of_stock_count' => $outOfStockCount,
            ],
            'sales_chart' => $salesChart,
            'top_products' => $topProducts,
            'top_categories' => $topCategories,
            'payment_breakdown' => $paymentBreakdown,
            'recent_sales' => $recentSales,
            'recent_movements' => $recentMovements,
            'low_stock_products' => $lowStockProducts,
        ]);
    }
}

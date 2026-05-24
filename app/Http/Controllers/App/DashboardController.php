<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sales;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $today = today();

        $todaySalesTotal = Sales::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->sum('total_amount');

        $todaySalesCount = Sales::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->count();

        $lowStockCount = Product::whereColumn('stock_quantity', '<=', 'min_stock_level')
            ->where('is_active', true)
            ->count();

        $totalProducts = Product::where('is_active', true)->count();

        $salesChart = collect(range(6, 0))->map(function (int $daysAgo) {
            $date = today()->subDays($daysAgo);

            return [
                'date' => $date->format('M d'),
                'total' => (float) Sales::whereDate('created_at', $date)
                    ->where('status', 'completed')
                    ->sum('total_amount'),
            ];
        })->values();

        $lowStockProducts = Product::with('category')
            ->whereColumn('stock_quantity', '<=', 'min_stock_level')
            ->where('is_active', true)
            ->orderBy('stock_quantity')
            ->limit(10)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => [
                'today_sales_total' => (float) $todaySalesTotal,
                'today_sales_count' => $todaySalesCount,
                'low_stock_count' => $lowStockCount,
                'total_products' => $totalProducts,
            ],
            'sales_chart' => $salesChart,
            'low_stock_products' => $lowStockProducts->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
                'stock_quantity' => $p->stock_quantity,
                'min_stock_level' => $p->min_stock_level,
                'category' => $p->category?->name,
            ])->values(),
        ]);
    }
}

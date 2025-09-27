<?php

namespace App\Filament\Pages;

use App\Models\Sales;
use App\Models\Product;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Pages\Concerns\InteractsWithHeaderActions;
use Filament\Actions\Action;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Reports extends Page
{
    use InteractsWithHeaderActions;
    
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    
    protected static ?string $navigationLabel = 'Reports';
    
    protected static ?string $title = 'Reports & Analytics';
    
    protected static ?int $navigationSort = 10;
    
    protected string $view = 'filament.pages.reports';

    public function getViewData(): array
    {
        // Get date ranges
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // Sales Analytics
        $totalSales = Sales::count();
        $totalRevenue = Sales::sum('total_amount');
        $todaySales = Sales::whereDate('created_at', $today)->count();
        $todayRevenue = Sales::whereDate('created_at', $today)->sum('total_amount');
        $weekSales = Sales::where('created_at', '>=', $thisWeek)->count();
        $weekRevenue = Sales::where('created_at', '>=', $thisWeek)->sum('total_amount');
        $monthSales = Sales::where('created_at', '>=', $thisMonth)->count();
        $monthRevenue = Sales::where('created_at', '>=', $thisMonth)->sum('total_amount');
        $lastMonthSales = Sales::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->count();
        $lastMonthRevenue = Sales::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->sum('total_amount');

        // Calculate growth
        $salesGrowth = $lastMonthSales > 0 ? (($monthSales - $lastMonthSales) / $lastMonthSales) * 100 : 0;
        $revenueGrowth = $lastMonthRevenue > 0 ? (($monthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;

        // Top Products
        $topProducts = DB::table('sales_items')
            ->join('products', 'sales_items.product_id', '=', 'products.id')
            ->select('products.name', 'products.sku', DB::raw('SUM(sales_items.quantity) as total_quantity'), DB::raw('SUM(sales_items.total_price) as total_revenue'))
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get();

        // Sales by Status
        $salesByStatus = Sales::select('status', DB::raw('count(*) as count'), DB::raw('sum(total_amount) as revenue'))
            ->groupBy('status')
            ->get();

        // Sales by Payment Method
        $salesByPayment = Sales::select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(total_amount) as revenue'))
            ->groupBy('payment_method')
            ->get();

        // Daily Sales (Last 7 days)
        $dailySales = Sales::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as sales_count'), DB::raw('sum(total_amount) as revenue'))
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Prepare daily sales data for chart
        $dailySalesChart = [];
        for($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayData = $dailySales->firstWhere('date', $date->format('Y-m-d'));
            $dailySalesChart[] = [
                'date' => $date->format('M j'),
                'sales_count' => $dayData->sales_count ?? 0,
                'revenue' => $dayData->revenue ?? 0
            ];
        }

        // Monthly Sales (Last 6 months)
        $monthlySales = Sales::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as sales_count'), DB::raw('sum(total_amount) as revenue'))
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Hourly Sales (Today)
        $hourlySales = Sales::select(DB::raw('HOUR(created_at) as hour'), DB::raw('count(*) as sales_count'), DB::raw('sum(total_amount) as revenue'))
            ->whereDate('created_at', $today)
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Service Requests Analytics
        $totalServiceRequests = ServiceRequest::count();
        $pendingRequests = ServiceRequest::where('status', 'pending')->count();
        $completedRequests = ServiceRequest::where('status', 'completed')->count();
        $inProgressRequests = ServiceRequest::where('status', 'in_progress')->count();

        // Average Order Value
        $averageOrderValue = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        // Conversion Rate (Service Requests to Sales)
        $conversionRate = $totalServiceRequests > 0 ? ($totalSales / $totalServiceRequests) * 100 : 0;

        // Top Staff (by sales)
        $topStaff = Sales::join('users', 'sales.user_id', '=', 'users.id')
            ->select('users.name', DB::raw('count(*) as sales_count'), DB::raw('sum(sales.total_amount) as total_revenue'))
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_revenue', 'desc')
            ->limit(5)
            ->get();

        // Inventory Status
        $lowStockProducts = Product::where('stock_quantity', '<=', DB::raw('min_stock_level'))->count();
        $outOfStockProducts = Product::where('stock_quantity', 0)->count();
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();

        return [
            'totalSales' => $totalSales,
            'totalRevenue' => $totalRevenue,
            'todaySales' => $todaySales,
            'todayRevenue' => $todayRevenue,
            'weekSales' => $weekSales,
            'weekRevenue' => $weekRevenue,
            'monthSales' => $monthSales,
            'monthRevenue' => $monthRevenue,
            'salesGrowth' => $salesGrowth,
            'revenueGrowth' => $revenueGrowth,
            'topProducts' => $topProducts,
            'salesByStatus' => $salesByStatus,
            'salesByPayment' => $salesByPayment,
            'dailySales' => $dailySales,
            'dailySalesChart' => $dailySalesChart,
            'monthlySales' => $monthlySales,
            'hourlySales' => $hourlySales,
            'averageOrderValue' => $averageOrderValue,
            'conversionRate' => $conversionRate,
            'totalServiceRequests' => $totalServiceRequests,
            'pendingRequests' => $pendingRequests,
            'completedRequests' => $completedRequests,
            'inProgressRequests' => $inProgressRequests,
            'topStaff' => $topStaff,
            'lowStockProducts' => $lowStockProducts,
            'outOfStockProducts' => $outOfStockProducts,
            'totalProducts' => $totalProducts,
            'activeProducts' => $activeProducts,
        ];
    }
}

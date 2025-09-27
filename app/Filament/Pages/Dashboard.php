<?php

namespace App\Filament\Pages;

use App\Models\Sales;
use App\Models\Product;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Category;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Dashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';
    
    protected static ?string $navigationLabel = 'Dashboard';
    
    protected static ?string $title = 'Dashboard';
    
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.dashboard';

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

        // Inventory Status
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $lowStockProducts = Product::whereRaw('stock_quantity <= min_stock_level')->count();
        $outOfStockProducts = Product::where('stock_quantity', 0)->count();
        $totalCategories = Category::count();
        $activeCategories = Category::where('is_active', true)->count();

        // Suppliers
        $totalSuppliers = Supplier::count();
        $activeSuppliers = Supplier::where('is_active', true)->count();

        // Service Requests
        $totalServiceRequests = ServiceRequest::count();
        $pendingRequests = ServiceRequest::where('status', 'pending')->count();
        $completedRequests = ServiceRequest::where('status', 'completed')->count();
        $inProgressRequests = ServiceRequest::where('status', 'in_progress')->count();

        // Users
        $totalUsers = User::count();
        $activeUsers = User::count(); // Assuming all users are active if no is_active column

        // Average Order Value
        $averageOrderValue = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        // Top Products (by sales)
        $topProducts = DB::table('sales_items')
            ->join('products', 'sales_items.product_id', '=', 'products.id')
            ->select('products.name', 'products.sku', DB::raw('SUM(sales_items.quantity) as total_quantity'), DB::raw('SUM(sales_items.total_price) as total_revenue'))
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get();

        // Recent Sales
        $recentSales = Sales::with(['user', 'salesItems.product'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent Service Requests
        $recentServiceRequests = ServiceRequest::with('service')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return [
            // Sales Data
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
            'averageOrderValue' => $averageOrderValue,

            // Inventory Data
            'totalProducts' => $totalProducts,
            'activeProducts' => $activeProducts,
            'lowStockProducts' => $lowStockProducts,
            'outOfStockProducts' => $outOfStockProducts,
            'totalCategories' => $totalCategories,
            'activeCategories' => $activeCategories,

            // Suppliers Data
            'totalSuppliers' => $totalSuppliers,
            'activeSuppliers' => $activeSuppliers,

            // Service Requests Data
            'totalServiceRequests' => $totalServiceRequests,
            'pendingRequests' => $pendingRequests,
            'completedRequests' => $completedRequests,
            'inProgressRequests' => $inProgressRequests,

            // Users Data
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,

            // Analytics Data
            'topProducts' => $topProducts,
            'recentSales' => $recentSales,
            'recentServiceRequests' => $recentServiceRequests,
        ];
    }
}

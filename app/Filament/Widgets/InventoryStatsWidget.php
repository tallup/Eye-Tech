<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\Service;
use App\Models\ServiceRequest;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InventoryStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Products', Product::count())
                ->description('Products in inventory')
                ->descriptionIcon('heroicon-m-cube')
                ->color('success'),
            Stat::make('Low Stock Items', Product::whereColumn('stock_quantity', '<=', 'min_stock_level')->count())
                ->description('Items needing restock')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),
            Stat::make('Active Suppliers', Supplier::where('is_active', true)->count())
                ->description('Active suppliers')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('info'),
            Stat::make('Pending Services', ServiceRequest::where('status', 'pending')->count())
                ->description('Services awaiting completion')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),
        ];
    }
}

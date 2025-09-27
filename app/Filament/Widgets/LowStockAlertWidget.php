<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Widgets\TableWidget;

class LowStockAlertWidget extends TableWidget
{
    protected static ?string $heading = 'Low Stock Alerts';
    
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->whereColumn('stock_quantity', '<=', 'min_stock_level')
                    ->where('is_active', true)
                    ->orderBy('stock_quantity', 'asc')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Product Name')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('stock_quantity')
                    ->label('Current Stock')
                    ->numeric()
                    ->badge()
                    ->color('danger')
                    ->sortable(),
                
                TextColumn::make('min_stock_level')
                    ->label('Min Required')
                    ->numeric()
                    ->badge()
                    ->color('warning')
                    ->sortable(),
                
                TextColumn::make('stock_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'out_of_stock' => 'danger',
                        'low_stock' => 'warning',
                        default => 'gray'
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'out_of_stock' => 'Out of Stock',
                        'low_stock' => 'Low Stock',
                        default => 'Unknown'
                    }),
                
                TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->sortable(),
                
                TextColumn::make('cost_price')
                    ->label('Cost Price')
                    ->formatStateUsing(fn ($state) => 'D' . number_format($state, 2))
                    ->sortable(),
            ])
            ->actions([
                Action::make('adjust_stock')
                    ->label('Adjust Stock')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->color('warning')
                    ->url(fn (Product $record): string => route('filament.admin.resources.products.edit', $record))
                    ->openUrlInNewTab(),
            ])
            ->emptyStateHeading('No Low Stock Items')
            ->emptyStateDescription('All products have sufficient stock levels.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}

<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->disk('public')
                    ->height(60)
                    ->width(60)
                    ->defaultImageUrl('/images/placeholder-product.svg'),
                
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('sku')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('brand')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('model')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('selling_price')
                    ->label('Selling Price')
                    ->formatStateUsing(fn ($state) => 'D' . number_format($state, 2))
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => match (true) {
                        $record->stock_quantity == 0 => 'danger',
                        $record->stock_quantity <= $record->min_stock_level => 'warning',
                        $record->stock_quantity <= $record->min_stock_level * 1.5 => 'info',
                        default => 'success'
                    })
                    ->formatStateUsing(fn ($state, $record) => $state . ' (Min: ' . $record->min_stock_level . ')'),
                
                \Filament\Tables\Columns\TextColumn::make('stock_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'out_of_stock' => 'danger',
                        'low_stock' => 'warning',
                        'warning' => 'info',
                        'in_stock' => 'success',
                        default => 'gray'
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'out_of_stock' => 'Out of Stock',
                        'low_stock' => 'Low Stock',
                        'warning' => 'Warning',
                        'in_stock' => 'In Stock',
                        default => 'Unknown'
                    }),
                
                \Filament\Tables\Columns\TextColumn::make('stock_value')
                    ->label('Stock Value')
                    ->formatStateUsing(fn ($state) => 'D' . number_format($state, 2))
                    ->sortable(),
                
                \Filament\Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                \Filament\Tables\Columns\TextColumn::make('category.name')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('supplier.name')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

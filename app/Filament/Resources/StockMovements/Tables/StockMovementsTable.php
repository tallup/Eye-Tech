<?php

namespace App\Filament\Resources\StockMovements\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class StockMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                
                \Filament\Tables\Columns\TextColumn::make('product.sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),
                
                \Filament\Tables\Columns\TextColumn::make('movement_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'in', 'purchase' => 'success',
                        'out', 'sale' => 'danger',
                        'adjustment' => 'warning',
                        'transfer' => 'info',
                        default => 'gray'
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'in' => 'Stock In',
                        'out' => 'Stock Out',
                        'adjustment' => 'Adjustment',
                        'transfer' => 'Transfer',
                        'sale' => 'Sale',
                        'purchase' => 'Purchase',
                        default => 'Unknown'
                    })
                    ->sortable(),
                
                \Filament\Tables\Columns\TextColumn::make('quantity_display')
                    ->label('Quantity Change')
                    ->badge()
                    ->color(fn ($record) => in_array($record->movement_type, ['in', 'purchase']) ? 'success' : 'danger')
                    ->sortable(),
                
                \Filament\Tables\Columns\TextColumn::make('previous_quantity')
                    ->label('Previous Stock')
                    ->numeric()
                    ->sortable(),
                
                \Filament\Tables\Columns\TextColumn::make('new_quantity')
                    ->label('New Stock')
                    ->numeric()
                    ->sortable(),
                
                \Filament\Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable(),
                
                \Filament\Tables\Columns\TextColumn::make('notes')
                    ->limit(50)
                    ->tooltip(function (\Filament\Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
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

<?php

namespace App\Filament\Resources\StockMovements\Schemas;

use App\Models\Product;
use Filament\Schemas\Schema;

class StockMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('product_id')
                    ->label('Product')
                    ->options(Product::where('is_active', true)->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $product = Product::find($state);
                            $set('previous_quantity', $product->stock_quantity);
                        }
                    }),
                
                \Filament\Forms\Components\Select::make('movement_type')
                    ->label('Movement Type')
                    ->options([
                        'in' => 'Stock In',
                        'out' => 'Stock Out',
                        'adjustment' => 'Adjustment',
                        'transfer' => 'Transfer',
                        'purchase' => 'Purchase',
                    ])
                    ->required()
                    ->reactive(),
                
                \Filament\Forms\Components\TextInput::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $previousQuantity = $get('previous_quantity');
                        $movementType = $get('movement_type');
                        
                        if ($previousQuantity !== null && $movementType) {
                            $newQuantity = $previousQuantity;
                            if (in_array($movementType, ['in', 'purchase'])) {
                                $newQuantity += $state;
                            } else {
                                $newQuantity -= $state;
                            }
                            $set('new_quantity', max(0, $newQuantity));
                        }
                    }),
                
                \Filament\Forms\Components\TextInput::make('previous_quantity')
                    ->label('Previous Stock')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false),
                
                \Filament\Forms\Components\TextInput::make('new_quantity')
                    ->label('New Stock')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false),
                
                \Filament\Forms\Components\TextInput::make('unit_cost')
                    ->label('Unit Cost (D)')
                    ->numeric()
                    ->prefix('D')
                    ->step(0.01),
                
                \Filament\Forms\Components\TextInput::make('total_cost')
                    ->label('Total Cost (D)')
                    ->numeric()
                    ->prefix('D')
                    ->step(0.01)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $quantity = $get('quantity');
                        $unitCost = $get('unit_cost');
                        
                        if ($quantity && $unitCost) {
                            $set('total_cost', $quantity * $unitCost);
                        }
                    }),
                
                \Filament\Forms\Components\Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3)
                    ->placeholder('Enter any additional notes about this stock movement...'),
            ]);
    }
}

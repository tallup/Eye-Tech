<?php

namespace App\Filament\Resources\Sales\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Components\Repeater;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class ViewSalesSchema
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Sale Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('sale_number')
                                    ->label('Sale Number')
                                    ->disabled()
                                    ->dehydrated(),
                                
                                TextInput::make('status')
                                    ->label('Status')
                                    ->disabled()
                                    ->dehydrated(),
                                
                                TextInput::make('created_at')
                                    ->label('Sale Date')
                                    ->disabled()
                                    ->dehydrated(),
                            ]),
                    ])
                    ->collapsible(),
                
                Section::make('Customer Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('customer_name')
                                    ->label('Customer Name')
                                    ->disabled()
                                    ->dehydrated(),
                                
                                TextInput::make('payment_method')
                                    ->label('Payment Method')
                                    ->disabled()
                                    ->dehydrated(),
                            ]),
                    ])
                    ->collapsible(),
                
                Section::make('Sale Items')
                    ->schema([
                        Repeater::make('salesItems')
                            ->label('Products')
                            ->relationship('salesItems')
                            ->schema([
                                Grid::make(6)
                                    ->schema([
                                        TextInput::make('item_name')
                                            ->label('Product')
                                            ->disabled()
                                            ->dehydrated(),
                                        
                                        TextInput::make('item_sku')
                                            ->label('SKU')
                                            ->disabled()
                                            ->dehydrated(),
                                        
                                        TextInput::make('quantity')
                                            ->label('Qty')
                                            ->disabled()
                                            ->dehydrated(),
                                        
                                        TextInput::make('unit_price')
                                            ->label('Unit Price')
                                            ->disabled()
                                            ->dehydrated(),
                                        
                                        TextInput::make('discount_amount')
                                            ->label('Discount')
                                            ->disabled()
                                            ->dehydrated(),
                                        
                                        TextInput::make('total_price')
                                            ->label('Total')
                                            ->disabled()
                                            ->dehydrated(),
                                    ]),
                            ])
                            ->columns(1),
                    ])
                    ->collapsible(),
                
                Section::make('Transaction Summary')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->disabled()
                                    ->dehydrated(),
                                
                                TextInput::make('discount_amount')
                                    ->label('Transaction Discount')
                                    ->disabled()
                                    ->dehydrated(),
                                
                                TextInput::make('total_amount')
                                    ->label('Total Amount')
                                    ->disabled()
                                    ->dehydrated(),
                            ]),
                    ])
                    ->collapsible(),
                
                Section::make('Additional Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('user.name')
                                    ->label('Sold By')
                                    ->disabled()
                                    ->dehydrated(),
                                
                                TextInput::make('notes')
                                    ->label('Notes')
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }
}

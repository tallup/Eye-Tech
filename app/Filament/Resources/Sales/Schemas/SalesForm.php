<?php

namespace App\Filament\Resources\Sales\Schemas;

use App\Models\Product;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class SalesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sale_number')
                    ->label('Sale Number')
                    ->required()
                    ->disabled()
                    ->dehydrated()
                    ->default('SALE-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6))),
                
                TextInput::make('customer_name')
                    ->label('Customer Name')
                    ->maxLength(255),
                
                Select::make('payment_method')
                    ->label('Payment Method')
                    ->options([
                        'cash' => 'Cash',
                        'card' => 'Card',
                        'mobile_money' => 'Mobile Money',
                        'bank_transfer' => 'Bank Transfer',
                    ])
                    ->default('cash')
                    ->required(),
                
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'refunded' => 'Refunded',
                    ])
                    ->default('pending')
                    ->required(),
                
                Repeater::make('sales_items')
                    ->label('Products')
                    ->relationship('salesItems')
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        // Calculate subtotal when items change
                        $subtotal = 0;
                        if (is_array($state)) {
                            foreach ($state as $item) {
                                if (isset($item['total_price']) && is_numeric($item['total_price'])) {
                                    $subtotal += $item['total_price'];
                                }
                            }
                        }
                        $set('subtotal', $subtotal);
                        
                        // Recalculate total amount
                        $discount = $get('discount_amount') ?? 0;
                        $set('total_amount', $subtotal - $discount);
                    })
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('category_filter')
                                    ->label('Filter by Category')
                                    ->options(\App\Models\Category::where('is_active', true)->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->placeholder('Select a category to filter products (optional)'),
                                
                                Select::make('product_id')
                                    ->label('Product')
                                    ->options(function (callable $get) {
                                        $categoryId = $get('category_filter');
                                        $query = Product::where('is_active', true)
                                            ->where('stock_quantity', '>', 0);
                                        
                                        if ($categoryId) {
                                            $query->where('category_id', $categoryId);
                                        }
                                        
                                        return $query->get()
                                            ->mapWithKeys(function ($product) {
                                                return [$product->id => $product->name . ' (' . $product->sku . ') - D' . number_format($product->selling_price, 2) . ' - Stock: ' . $product->stock_quantity];
                                            });
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        if ($state) {
                                            $product = Product::find($state);
                                            if ($product) {
                                                $set('item_name', $product->name);
                                                $set('item_sku', $product->sku);
                                                $set('item_description', $product->description);
                                                $set('unit_price', $product->selling_price);
                                                // Calculate total price when product is selected
                                                $quantity = $get('quantity') ?? 1;
                                                $discount = $get('discount_amount') ?? 0;
                                                $set('total_price', ($quantity * $product->selling_price) - $discount);
                                            }
                                        }
                                    }),
                            ]),
                        
                        Grid::make(4)
                            ->schema([
                                TextInput::make('quantity')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $unitPrice = $get('unit_price') ?? 0;
                                        $discount = $get('discount_amount') ?? 0;
                                        $set('total_price', ($state * $unitPrice) - $discount);
                                    }),
                                
                                TextInput::make('unit_price')
                                    ->label('Unit Price (D)')
                                    ->numeric()
                                    ->prefix('D')
                                    ->step(0.01)
                                    ->disabled()
                                    ->dehydrated()
                                    ->default(0),
                                
                                TextInput::make('discount_amount')
                                    ->label('Item Discount (D)')
                                    ->numeric()
                                    ->prefix('D')
                                    ->step(0.01)
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $quantity = $get('quantity') ?? 1;
                                        $unitPrice = $get('unit_price') ?? 0;
                                        $set('total_price', ($quantity * $unitPrice) - ($state ?? 0));
                                    }),
                                
                                TextInput::make('total_price')
                                    ->label('Total Price (D)')
                                    ->numeric()
                                    ->prefix('D')
                                    ->step(0.01)
                                    ->disabled()
                                    ->dehydrated(),
                            ]),
                        
                        // Hidden fields for item details
                        Hidden::make('item_name'),
                        Hidden::make('item_sku'),
                        Hidden::make('item_description'),
                    ])
                    ->addActionLabel('Add Product')
                    ->defaultItems(1)
                    ->collapsible()
                    ->cloneable()
                    ->deleteAction(
                        fn ($action) => $action->requiresConfirmation()
                    ),
                
                Section::make('Transaction Summary')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('subtotal')
                                    ->label('Subtotal (D)')
                                    ->numeric()
                                    ->prefix('D')
                                    ->step(0.01)
                                    ->disabled()
                                    ->dehydrated()
                                    ->default(0),
                                
                                TextInput::make('discount_amount')
                                    ->label('Transaction Discount (D)')
                                    ->numeric()
                                    ->prefix('D')
                                    ->step(0.01)
                                    ->default(0.0)
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $subtotal = $get('subtotal') ?? 0;
                                        $discount = $state ?? 0;
                                        $set('total_amount', $subtotal - $discount);
                                    }),
                                
                                TextInput::make('total_amount')
                                    ->label('Total Amount (D)')
                                    ->numeric()
                                    ->prefix('D')
                                    ->step(0.01)
                                    ->disabled()
                                    ->dehydrated(),
                            ]),
                    ])
                    ->collapsible(),
                
                Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }
}

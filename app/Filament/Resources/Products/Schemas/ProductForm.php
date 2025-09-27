<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Category;
use App\Models\Supplier;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Product Name'),
                
                \Filament\Forms\Components\TextInput::make('sku')
                    ->required()
                    ->maxLength(255)
                    ->label('SKU')
                    ->unique(ignoreRecord: true),
                
                \Filament\Forms\Components\Textarea::make('description')
                    ->rows(3)
                    ->label('Description')
                    ->nullable(),
                
                \Filament\Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->options(Category::where('is_active', true)->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload(),
                
                \Filament\Forms\Components\Select::make('supplier_id')
                    ->label('Supplier')
                    ->options(Supplier::where('is_active', true)->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload(),
                
                \Filament\Forms\Components\TextInput::make('brand')
                    ->maxLength(255)
                    ->label('Brand'),
                
                \Filament\Forms\Components\TextInput::make('model')
                    ->maxLength(255)
                    ->label('Model'),
                
                \Filament\Forms\Components\TextInput::make('cost_price')
                    ->label('Cost Price (D)')
                    ->numeric()
                    ->prefix('D')
                    ->step(0.01)
                    ->required(),
                
                \Filament\Forms\Components\TextInput::make('selling_price')
                    ->label('Selling Price (D)')
                    ->numeric()
                    ->prefix('D')
                    ->step(0.01)
                    ->required(),
                
                \Filament\Forms\Components\TextInput::make('stock_quantity')
                    ->label('Stock Quantity')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
                
                \Filament\Forms\Components\TextInput::make('min_stock_level')
                    ->label('Minimum Stock Level')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
                
                \Filament\Forms\Components\Textarea::make('specifications')
                    ->rows(4)
                    ->label('Specifications')
                    ->placeholder('Enter specifications as JSON or plain text...')
                    ->nullable(),
                
                \Filament\Forms\Components\FileUpload::make('image')
                    ->label('Product Image')
                    ->image()
                    ->directory('products')
                    ->disk('public')
                    ->visibility('public')
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->maxSize(2048)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->imageResizeTargetWidth('800')
                    ->imageResizeTargetHeight('600')
                    ->imageResizeMode('cover')
                    ->helperText('Upload a product image (JPG, PNG, WebP). Max size: 2MB. Recommended: 800x600px')
                    ->nullable(),
                
                \Filament\Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }
}

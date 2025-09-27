<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Service Name'),
                
                \Filament\Forms\Components\TextInput::make('slug')
                    ->maxLength(255)
                    ->label('URL Slug')
                    ->unique(ignoreRecord: true)
                    ->helperText('Auto-generated from name if left empty'),
                
                \Filament\Forms\Components\Textarea::make('description')
                    ->rows(4)
                    ->label('Description')
                    ->required(),
                
                \Filament\Forms\Components\TextInput::make('price')
                    ->label('Price (D)')
                    ->numeric()
                    ->prefix('D')
                    ->step(0.01)
                    ->required(),
                
                \Filament\Forms\Components\TextInput::make('estimated_duration')
                    ->label('Estimated Duration (minutes)')
                    ->numeric()
                    ->minValue(1)
                    ->required(),
                
                \Filament\Forms\Components\TextInput::make('category')
                    ->maxLength(255)
                    ->label('Category')
                    ->required(),
                
                \Filament\Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }
}

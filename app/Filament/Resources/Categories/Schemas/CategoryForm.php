<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Category Name'),
                
                \Filament\Forms\Components\TextInput::make('slug')
                    ->maxLength(255)
                    ->label('URL Slug')
                    ->unique(ignoreRecord: true)
                    ->helperText('Auto-generated from name if left empty'),
                
                \Filament\Forms\Components\Textarea::make('description')
                    ->rows(3)
                    ->label('Description')
                    ->nullable(),
                
                \Filament\Forms\Components\TextInput::make('icon')
                    ->maxLength(255)
                    ->label('Icon')
                    ->placeholder('e.g., 📱, 🔧, 💻')
                    ->helperText('Icon to display for this category'),
                
                \Filament\Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }
}

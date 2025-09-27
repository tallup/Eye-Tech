<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Supplier Name'),
                
                \Filament\Forms\Components\TextInput::make('email')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->label('Email')
                    ->nullable(),
                
                \Filament\Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(255)
                    ->label('Phone')
                    ->nullable(),
                
                \Filament\Forms\Components\TextInput::make('contact_person')
                    ->maxLength(255)
                    ->label('Contact Person')
                    ->nullable(),
                
                \Filament\Forms\Components\Textarea::make('address')
                    ->rows(3)
                    ->label('Address')
                    ->nullable(),
                
                \Filament\Forms\Components\Textarea::make('notes')
                    ->rows(3)
                    ->label('Notes')
                    ->nullable(),
                
                \Filament\Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }
}

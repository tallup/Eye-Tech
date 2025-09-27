<?php

namespace App\Filament\Resources\ServiceRequests\Schemas;

use App\Models\Service;
use Filament\Schemas\Schema;

class ServiceRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('customer_name')
                    ->required()
                    ->maxLength(255)
                    ->label('Customer Name'),
                
                \Filament\Forms\Components\TextInput::make('customer_phone')
                    ->required()
                    ->maxLength(255)
                    ->label('Customer Phone')
                    ->tel(),
                
                \Filament\Forms\Components\TextInput::make('customer_email')
                    ->email()
                    ->maxLength(255)
                    ->label('Customer Email')
                    ->nullable(),
                
                \Filament\Forms\Components\Select::make('service_id')
                    ->label('Service')
                    ->options(Service::where('is_active', true)->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload(),
                
                \Filament\Forms\Components\TextInput::make('request_number')
                    ->label('Request Number')
                    ->default('SR-' . strtoupper(\Illuminate\Support\Str::random(8)))
                    ->disabled()
                    ->dehydrated(),
                
                \Filament\Forms\Components\Textarea::make('device_description')
                    ->required()
                    ->rows(3)
                    ->label('Device Description')
                    ->placeholder('e.g., iPhone 13 Pro, Samsung Galaxy S21, etc.'),
                
                \Filament\Forms\Components\Textarea::make('problem_description')
                    ->required()
                    ->rows(4)
                    ->label('Problem Description')
                    ->placeholder('Describe the issue or problem with the device...'),
                
                \Filament\Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('pending')
                    ->required(),
                
                \Filament\Forms\Components\TextInput::make('estimated_cost')
                    ->label('Estimated Cost (D)')
                    ->numeric()
                    ->prefix('D')
                    ->step(0.01)
                    ->nullable(),
                
                \Filament\Forms\Components\TextInput::make('final_cost')
                    ->label('Final Cost (D)')
                    ->numeric()
                    ->prefix('D')
                    ->step(0.01)
                    ->nullable(),
                
                \Filament\Forms\Components\DateTimePicker::make('completed_at')
                    ->label('Completed At')
                    ->nullable(),
                
                \Filament\Forms\Components\Textarea::make('notes')
                    ->rows(3)
                    ->label('Notes')
                    ->placeholder('Any additional notes or comments...')
                    ->nullable(),
            ]);
    }
}

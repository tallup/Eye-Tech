<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile Information')
                    ->schema([
                        FileUpload::make('profile_picture')
                            ->label('Profile Picture')
                            ->image()
                            ->directory('users')
                            ->disk('public')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',
                                '16:9',
                                '4:3',
                            ])
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->imageResizeTargetWidth('300')
                            ->imageResizeTargetHeight('300')
                            ->imageResizeMode('cover')
                            ->helperText('Upload a profile picture (JPG, PNG, WebP). Max size: 2MB. Recommended: 300x300px')
                            ->nullable(),
                        
                        TextInput::make('first_name')
                            ->label('First Name')
                            ->maxLength(255)
                            ->required(),
                        
                        TextInput::make('last_name')
                            ->label('Last Name')
                            ->maxLength(255)
                            ->required(),
                        
                        TextInput::make('name')
                            ->label('Full Name')
                            ->maxLength(255)
                            ->required()
                            ->helperText('This will be auto-generated from first and last name if left empty'),
                    ])
                    ->columns(2),
                
                Section::make('Contact Information')
                    ->schema([
                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->maxLength(255)
                            ->required()
                            ->placeholder('+220 123 4567'),
                        
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->maxLength(255)
                            ->nullable()
                            ->helperText('Email is optional - phone number is the primary contact method'),
                    ])
                    ->columns(2),
                
                Section::make('Account Security')
                    ->schema([
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->helperText('Minimum 8 characters'),
                        
                        DateTimePicker::make('email_verified_at')
                            ->label('Email Verified At')
                            ->nullable()
                            ->helperText('Leave empty if email is not verified'),
                    ])
                    ->columns(2),
            ]);
    }
}

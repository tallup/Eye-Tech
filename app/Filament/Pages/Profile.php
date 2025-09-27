<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use BackedEnum;

class Profile extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user';

    protected static ?string $title = 'My Profile';

    protected static ?string $navigationLabel = 'Profile';

    protected static ?int $navigationSort = 100;

    protected static bool $shouldRegisterNavigation = false; // Hide from navigation since it's in user menu

    protected string $view = 'filament.pages.profile';

    public function mount(): void
    {
        // No form initialization needed for view-only page
    }
}

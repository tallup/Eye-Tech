<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\Profile;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('EyeTech')
            ->brandLogo(asset('images/logo.png'))
            ->favicon(asset('images/favicon.ico'))
            ->colors([
                'primary' => Color::Red,
                'danger' => Color::Red,
                'gray' => Color::Gray,
            ])
            ->font('Inter')
            ->topNavigation()
            ->userMenuItems([
                \Filament\Navigation\MenuItem::make()
                    ->label('Profile')
                    ->url('/admin/profile')
                    ->icon('heroicon-o-user')
                    ->sort(1),
                \Filament\Navigation\MenuItem::make()
                    ->label('Log out')
                    ->url('/admin/logout')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->sort(999),
            ])
            ->renderHook(
                'panels::styles.after',
                fn (): string => '<style>
                    /* Hide sidebar when using top navigation */
                    .fi-sidebar { display: none !important; }
                    .fi-main { margin-left: 0 !important; }
                    
                    /* Center top navigation with proper spacing */
                    .fi-topbar {
                        display: flex !important;
                        align-items: center !important;
                        justify-content: space-between !important;
                        width: 100% !important;
                    }
                    
                    /* Logo section */
                    .fi-topbar-brand {
                        flex-shrink: 0 !important;
                        width: auto !important;
                    }
                    
                    /* Navigation section - centered */
                    .fi-topbar-nav {
                        flex: 1 !important;
                        display: flex !important;
                        justify-content: center !important;
                        align-items: center !important;
                        margin: 0 !important;
                        padding: 0 !important;
                    }
                    
                    .fi-topbar-nav-list {
                        display: flex !important;
                        justify-content: center !important;
                        align-items: center !important;
                        width: 100% !important;
                        margin: 0 !important;
                        padding: 0 !important;
                        list-style: none !important;
                    }
                    
                    .fi-topbar-nav-item {
                        margin: 0 12px !important;
                        font-size: 16px !important;
                        font-weight: 500 !important;
                    }
                    
                    .fi-topbar-nav-item a {
                        font-size: 16px !important;
                        font-weight: 500 !important;
                        padding: 8px 16px !important;
                        text-decoration: none !important;
                    }
                    
                    /* User menu section */
                    .fi-topbar-actions {
                        flex-shrink: 0 !important;
                        width: auto !important;
                    }
                    
                    /* Center main content */
                    .fi-main { 
                        max-width: 1400px !important; 
                        margin: 0 auto !important; 
                        padding: 0 20px !important;
                    }
                    .fi-page-content {
                        max-width: 1400px !important;
                        margin: 0 auto !important;
                    }
                    
                    /* Fix dropdown behavior */
                    .fi-dropdown-panel {
                        z-index: 9999 !important;
                        position: absolute !important;
                    }
                    .fi-dropdown-list {
                        z-index: 9999 !important;
                    }
                </style>'
            )
            ->renderHook(
                'panels::scripts.after',
                fn (): string => '<script>
                    // Define missing toggle function
                    window.toggle = function(event) {
                        if (event && event.preventDefault) {
                            event.preventDefault();
                        }
                        return false;
                    };
                    
                    // Fix Alpine.js store issues
                    document.addEventListener("alpine:init", () => {
                        // Initialize missing Alpine.js stores
                        if (typeof Alpine.store === "function") {
                            Alpine.store("sidebar", {
                                groupIsCollapsed: (group) => true, // Default to collapsed
                                toggleGroupCollapsed: (group) => {},
                                isCollapsed: true, // Default to collapsed
                                toggleCollapsed: () => {}
                            });
                            
                            Alpine.store("theme", {
                                mode: "light"
                            });
                        }
                    });
                    
                    // Define missing Filament functions
                    window.filamentActionModals = window.filamentActionModals || {
                        open: () => {},
                        close: () => {}
                    };
                    
                    window.filamentDropdown = window.filamentDropdown || {
                        open: () => {},
                        close: () => {}
                    };
                    
                    document.addEventListener("DOMContentLoaded", function() {
                        // Simple click outside handler
                        document.addEventListener("click", function(e) {
                            // Check if click is outside any dropdown
                            const isInsideDropdown = e.target.closest(".fi-dropdown-panel") || 
                                                   e.target.closest(".fi-dropdown-list") ||
                                                   e.target.closest(".fi-topbar-nav-item");
                            
                            if (!isInsideDropdown) {
                                // Close all open dropdowns by removing Alpine.js data
                                const openDropdowns = document.querySelectorAll(".fi-dropdown-panel");
                                openDropdowns.forEach(dropdown => {
                                    if (dropdown._x_dataStack && dropdown._x_dataStack[0]) {
                                        dropdown._x_dataStack[0].isOpen = false;
                                    }
                                });
                            }
                        });
                    });
                </script>'
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
                Profile::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                \App\Filament\Widgets\InventoryStatsWidget::class,
                \App\Filament\Widgets\LowStockAlertWidget::class,
            ])
            ->navigationGroups([
                'Inventory' => 'Inventory Management',
                'Sales' => 'Sales & Orders',
                'Users' => 'User Management',
                'Reports' => 'Reports & Analytics',
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}

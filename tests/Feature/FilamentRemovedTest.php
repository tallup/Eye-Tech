<?php

namespace Tests\Feature;

use Tests\TestCase;

class FilamentRemovedTest extends TestCase
{
    public function test_admin_route_returns_404(): void
    {
        $this->get('/admin')->assertNotFound();
    }

    public function test_admin_login_route_returns_404(): void
    {
        $this->get('/admin/login')->assertNotFound();
    }

    public function test_no_filament_classes_loaded(): void
    {
        $this->assertFalse(class_exists('Filament\\Panel'), 'Filament\\Panel must not be autoloadable.');
        $this->assertFalse(class_exists('Filament\\FilamentManager'), 'Filament\\FilamentManager must not be autoloadable.');
        $this->assertFalse(class_exists('Filament\\Models\\Contracts\\FilamentUser'), 'FilamentUser contract must not be autoloadable.');
    }

    public function test_composer_json_does_not_list_filament(): void
    {
        $composer = json_decode(file_get_contents(base_path('composer.json')), true);
        $this->assertIsArray($composer);
        $this->assertArrayNotHasKey('filament/filament', $composer['require'] ?? []);
        $this->assertArrayNotHasKey('filament/filament', $composer['require-dev'] ?? []);
    }

    public function test_app_filament_directory_is_gone(): void
    {
        $this->assertDirectoryDoesNotExist(base_path('app/Filament'));
        $this->assertDirectoryDoesNotExist(base_path('app/Providers/Filament'));
    }
}

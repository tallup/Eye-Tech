<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserModelNoFilamentTest extends TestCase
{
    public function test_user_model_does_not_implement_filament_user_contract(): void
    {
        $this->assertNotContains(
            'Filament\\Models\\Contracts\\FilamentUser',
            class_implements(User::class) ?: [],
            'User model must not implement FilamentUser contract after Phase 4.'
        );
    }

    public function test_user_model_has_no_can_access_panel_method(): void
    {
        $this->assertFalse(
            method_exists(User::class, 'canAccessPanel'),
            'User::canAccessPanel() must be removed in Phase 4.'
        );
    }
}

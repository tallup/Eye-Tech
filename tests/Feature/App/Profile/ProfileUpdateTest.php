<?php

namespace Tests\Feature\App\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_profile_edit_renders(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withoutVite()
            ->get('/app/profile')
            ->assertOk()
            ->assertInertia(fn ($p) => $p
                ->component('Profile/Edit')
                ->where('profile.id', $user->id));
    }

    public function test_user_can_update_own_name(): void
    {
        $user = User::factory()->create(['name' => 'Old']);

        $this->actingAs($user)
            ->put('/app/profile', [
                'name' => 'New',
                'email' => $user->email,
            ])
            ->assertRedirect('/app/profile');

        $this->assertEquals('New', $user->fresh()->name);
    }

    public function test_password_change_requires_correct_current_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('correct')]);

        $this->actingAs($user)
            ->put('/app/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'current_password' => 'wrong',
                'password' => 'newpass1',
                'password_confirmation' => 'newpass1',
            ])
            ->assertSessionHasErrors('current_password');
    }

    public function test_user_can_change_password_with_correct_current(): void
    {
        $user = User::factory()->create(['password' => Hash::make('correct')]);

        $this->actingAs($user)
            ->put('/app/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'current_password' => 'correct',
                'password' => 'newpass1',
                'password_confirmation' => 'newpass1',
            ])
            ->assertRedirect('/app/profile');

        $this->assertTrue(Hash::check('newpass1', $user->fresh()->password));
    }

    public function test_update_without_password_keeps_old_hash(): void
    {
        $user = User::factory()->create(['password' => Hash::make('correct')]);
        $oldHash = $user->password;

        $this->actingAs($user)
            ->put('/app/profile', [
                'name' => 'Renamed',
                'email' => $user->email,
            ])
            ->assertRedirect('/app/profile');

        $this->assertEquals($oldHash, $user->fresh()->password);
    }
}

<?php

namespace Tests\Feature\Website;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_contact_submission_flashes_status(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Awa',
            'phone' => '+220 700 0000',
            'message' => 'Need to unlock a phone today.',
        ]);

        $response->assertRedirect('/contact');
        $response->assertSessionHas('status');
    }

    public function test_invalid_contact_submission_returns_errors(): void
    {
        $response = $this->from('/contact')->post('/contact', []);

        $response->assertRedirect('/contact');
        $response->assertSessionHasErrors(['name', 'phone', 'message']);
    }
}

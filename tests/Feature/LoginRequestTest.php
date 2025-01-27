<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginRequestTest extends TestCase
{
    public function test_required_username_response_login(): void
    {
        $response = $this->postJson('/api/login', [
            'password' => 'password',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'username' => 'The username field is required.'
            ]);
    }

    public function test_required_password_response_login(): void
    {
        $response = $this->postJson('/api/login', [
            'username' => 'username',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'password' => 'The password field is required.'
            ]);
    }

    public function test_short_password_response_login(): void
    {
        $response = $this->postJson('/api/login', [
            'username' => 'username',
            'password' => 'short'
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'password' => 'The password field must be at least 8 characters.'
            ]);
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use Hash;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create([
            'username' => 'root',
            'password' => Hash::make('password'),
        ]);
    }

    public function test_wrong_username_response_login(): void
    {
        $response = $this->postJson('/api/login', [
            'username' => 'wrong123',
            'password' => 'password',
        ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthorized.',
            ]);
    }
    
    public function test_wrong_password_response_login(): void
    {
        $response = $this->postJson('/api/login', [
            'username' => 'root',
            'password' => 'wrong123',
        ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthorized.',
            ]);
    }

    public function test_authorized_user_response_login(): void
    {
        $response = $this->postJson('/api/login', [
            'username' => 'root',
            'password' => 'password',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'token',
                ]
            ]);
    }
}

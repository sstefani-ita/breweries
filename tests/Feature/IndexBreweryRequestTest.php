<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\DataProvider;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use Hash;

class IndexBreweryRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_required_token_laravel_sanctum(): void
    {
        $response = $this->getJson('/api/v1/data?per_page=10&page=1');

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public static function invalidRequests(): array
    {
        return [
            ['perPage' => 'ABC', 'page' => 1, 'errors' => 'per_page'],
            ['perPage' => -1, 'page' => 1, 'errors' => 'per_page'],
            ['perPage' => 10, 'page' => 'ABC', 'errors' => 'page'],
            ['perPage' => 10, 'page' => -1, 'errors' => 'page'],
            ['perPage' => 'ABC', 'page' => 'ABC', 'errors' => ['per_page', 'page']],
            ['perPage' => -1, 'page' => -1, 'errors' => ['per_page', 'page']],
        ];
    }

    #[DataProvider('invalidRequests')]
    public function test_index_return_bad_requests($perPage, $page, $errors): void
    {
        Sanctum::actingAs(User::factory()->create([
            'username' => 'root',
            'password' => Hash::make('password'),
        ]));

        $response = $this->getJson('/api/v1/data?per_page=' . $perPage . '&page=' . $page);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors($errors);
    }
}

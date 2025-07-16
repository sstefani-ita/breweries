<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Laravel\Sanctum\Sanctum;
use App\Models\User;
use Hash;
use Http;

class BreweryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(User::factory()->create([
            'username' => 'root',
            'password' => Hash::make('password'),
        ]));

        $this->apiOpenBreweryDbUrl = config('services.api_open_brewery_db.url');
    }

    public function test_index_return_error_when_api_fails(): void
    {
        Http::fake([
            $this->apiOpenBreweryDbUrl . '?per_page=10&page=1' => Http::response(null, 500),
        ]);

        $response = $this->getJson('/api/v1/data?per_page=10&page=1');

        $response->assertStatus(500)
            ->assertJson([
                'message' => 'Failed.',
            ]);
    }

    public function test_index_return_error_when_meta_api_fails(): void
    {
        Http::fake([
            $this->apiOpenBreweryDbUrl . '?per_page=10&page=1' => Http::response([
                []
            ], 200),
            $this->apiOpenBreweryDbUrl . '/meta' => Http::response(null, 500),
        ]);

        $response = $this->getJson('/api/v1/data?per_page=10&page=1');

        $response->assertStatus(500)
            ->assertJson([
                'message' => 'Failed.',
            ]);
    }

    public function test_index_json_structure_successful_response(): void
    {
        $response = $this->getJson('/api/v1/data?per_page=10&page=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta',
            ]);
    }

    public function test_api_returns_successful_real_data_response(): void
    {
        $response = $this->getJson('/api/v1/data?per_page=1&page=1');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'id' => '5128df48-79fc-4f0f-8b52-d06be54d0cec',
                        'name' => '(405) Brewing Co'
                    ]
                ]
            ]);
    }

    public function test_api_returns_successful_real_links_response(): void
    {
        $per_page = 10;
        $page = 2;

        $response = $this->getJson('/api/v1/data?per_page=' . $per_page . '&page=' . $page);

        $response->assertStatus(200)
            ->assertJson([
                'links' => [
                    'first' => 'http://localhost/api/v1/data?per_page=' . $per_page . '&page=' . ($page - 1),
                    'last' => 'http://localhost/api/v1/data?per_page=' . $per_page . '&page=840',
                    'prev' => 'http://localhost/api/v1/data?per_page=' . $per_page . '&page=' . ($page - 1),
                    'next' => 'http://localhost/api/v1/data?per_page=' . $per_page . '&page=' . ($page + 1)
                ]
            ]);
    }

    public function test_api_returns_successful_real_meta_response(): void
    {
        $response = $this->getJson('/api/v1/data?per_page=10&page=1');

        $response->assertStatus(200)
            ->assertJson([
                'meta' => [
                    'total' => 8393,
                ]
            ]);
    }
}

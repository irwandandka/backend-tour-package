<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.internal.api_key' => 'test-api-key']);
    }

    private function headers(array $extra = []): array
    {
        return array_merge(['X-API-KEY' => 'test-api-key'], $extra);
    }

    private function authHeaders(): array
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        return $this->headers(['Authorization' => 'Bearer '.$token]);
    }

    public function test_list_is_public(): void
    {
        City::factory()->create();

        $this->withHeaders($this->headers())
            ->getJson('/api/v1/city/list')
            ->assertOk();
    }

    public function test_show_is_public(): void
    {
        $city = City::factory()->create();

        $this->withHeaders($this->headers())
            ->getJson("/api/v1/city/{$city->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $city->id);
    }

    public function test_delete_without_token_is_unauthorized(): void
    {
        $city = City::factory()->create();

        $this->withHeaders($this->headers())
            ->deleteJson("/api/v1/city/{$city->id}")
            ->assertStatus(401);

        $this->assertNotSoftDeleted('cities', ['id' => $city->id]);
    }

    public function test_deleted_list_without_token_is_unauthorized(): void
    {
        $this->withHeaders($this->headers())
            ->getJson('/api/v1/city/deleted')
            ->assertStatus(401);
    }

    public function test_delete_with_valid_token_succeeds(): void
    {
        $city = City::factory()->create();

        $this->withHeaders($this->authHeaders())
            ->deleteJson("/api/v1/city/{$city->id}")
            ->assertOk();

        $this->assertSoftDeleted('cities', ['id' => $city->id]);
    }
}

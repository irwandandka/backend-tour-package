<?php

namespace Tests\Feature;

use App\Services\GoogleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckApiKeyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.internal.api_key' => 'test-api-key']);
    }

    public function test_request_without_api_key_header_is_rejected(): void
    {
        $this->getJson('/api/v1/region/list')
            ->assertStatus(401);
    }

    public function test_request_with_wrong_api_key_is_rejected(): void
    {
        $this->withHeaders(['X-API-KEY' => 'wrong-key'])
            ->getJson('/api/v1/region/list')
            ->assertStatus(401);
    }

    public function test_request_with_correct_api_key_is_allowed(): void
    {
        $this->withHeaders(['X-API-KEY' => 'test-api-key'])
            ->getJson('/api/v1/region/list')
            ->assertOk();
    }

    public function test_request_is_rejected_when_no_api_key_is_configured(): void
    {
        // fail closed: an empty/unset expected key must never let requests through
        config(['services.internal.api_key' => null]);

        $this->getJson('/api/v1/region/list')
            ->assertStatus(401);
    }

    public function test_google_callback_bypasses_api_key_check(): void
    {
        $this->mock(GoogleService::class, function ($mock) {
            $mock->shouldReceive('getUserData')->andReturn([
                'id' => 'google-123',
                'name' => 'Test User',
                'email' => 'googleuser@example.com',
                'avatar' => null,
            ]);
        });

        // no X-API-KEY header sent at all, must not be blocked by the API key check
        $this->getJson('/api/v1/auth/google/callback?code=fake-code&redirect_uri=http://localhost')
            ->assertOk();
    }
}

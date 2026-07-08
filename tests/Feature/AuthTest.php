<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
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

    public function test_register_creates_a_new_user(): void
    {
        $response = $this->withHeaders($this->headers())->postJson('/api/v1/auth/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);
        $this->assertArrayNotHasKey('password', $response->json('user'));
    }

    public function test_register_rejects_duplicate_email(): void
    {
        User::factory()->create(['email' => 'dupe@example.com']);

        $response = $this->withHeaders($this->headers())->postJson('/api/v1/auth/register', [
            'name' => 'Jane Doe',
            'email' => 'dupe@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
    }

    public function test_register_rejects_short_password(): void
    {
        $response = $this->withHeaders($this->headers())->postJson('/api/v1/auth/register', [
            'name' => 'Jane Doe',
            'email' => 'jane2@example.com',
            'password' => 'short',
        ]);

        $response->assertStatus(422);
    }

    public function test_login_returns_token_for_valid_credentials(): void
    {
        User::factory()->create([
            'email' => 'login@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->withHeaders($this->headers())->postJson('/api/v1/auth/login', [
            'email' => 'login@example.com',
            'password' => 'secret123',
        ]);

        $response->assertOk();
        $this->assertNotEmpty($response->json('access_token'));
    }

    public function test_login_rejects_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'login2@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->withHeaders($this->headers())->postJson('/api/v1/auth/login', [
            'email' => 'login2@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
    }

    public function test_login_rejects_unknown_email(): void
    {
        $response = $this->withHeaders($this->headers())->postJson('/api/v1/auth/login', [
            'email' => 'doesnotexist@example.com',
            'password' => 'whatever123',
        ]);

        $response->assertStatus(401);
    }

    public function test_logout_revokes_the_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withHeaders($this->headers(['Authorization' => 'Bearer '.$token]))
            ->postJson('/api/v1/auth/logout')
            ->assertOk();

        $this->withHeaders($this->headers(['Authorization' => 'Bearer '.$token]))
            ->getJson('/api/v1/user/profile')
            ->assertStatus(401);
    }

    public function test_protected_endpoint_requires_token(): void
    {
        $this->withHeaders($this->headers())
            ->getJson('/api/v1/user/profile')
            ->assertStatus(401)
            ->assertJsonPath('code', 'token_missing');
    }

    public function test_protected_endpoint_rejects_invalid_token(): void
    {
        $this->withHeaders($this->headers(['Authorization' => 'Bearer garbage-token']))
            ->getJson('/api/v1/user/profile')
            ->assertStatus(401)
            ->assertJsonPath('code', 'token_invalid');
    }

    public function test_protected_endpoint_rejects_expired_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test');
        $token->accessToken->forceFill(['expires_at' => now()->subDay()])->save();

        $this->withHeaders($this->headers(['Authorization' => 'Bearer '.$token->plainTextToken]))
            ->getJson('/api/v1/user/profile')
            ->assertStatus(401)
            ->assertJsonPath('code', 'token_expired');
    }

    public function test_protected_endpoint_succeeds_with_valid_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withHeaders($this->headers(['Authorization' => 'Bearer '.$token]))
            ->getJson('/api/v1/user/profile')
            ->assertOk()
            ->assertJsonPath('data.id', $user->id);
    }
}

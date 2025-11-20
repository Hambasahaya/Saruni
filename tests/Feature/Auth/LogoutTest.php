<?php

namespace Tests\Feature\Auth;

use App\Models\Admin;
use App\Models\Session;
use App\Services\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.jwt_secret' => 'testing-secret']);
    }

    public function test_logout_rotates_token(): void
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $jwt = app(JwtService::class);
        $token = $jwt->generateToken($admin->id, 'admin');

        Session::create([
            'user_id' => $admin->id,
            'token' => $token,
            'role' => 'admin',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/logout');

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'logout berhasil',
            ])
            ->assertJsonMissingPath('data.new_token');

        $this->assertDatabaseMissing('sessions', [
            'token' => $token,
        ]);
    }
}

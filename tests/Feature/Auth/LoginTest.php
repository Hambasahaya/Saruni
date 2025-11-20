<?php

namespace Tests\Feature\Auth;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.jwt_secret' => 'testing-secret']);
    }

    public function test_admin_can_login(): void
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'secret123',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'login berhasil',
                'data' => [
                    'role' => 'admin',
                ],
            ]);

        $this->assertDatabaseHas('sessions', [
            'user_id' => $admin->id,
            'role' => 'admin',
        ]);
    }

    public function test_login_with_invalid_password_is_rejected(): void
    {
        Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
    }

    public function test_login_with_unknown_email_is_rejected(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'unknown@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'email tidak ditemukan',
            ]);
    }
}

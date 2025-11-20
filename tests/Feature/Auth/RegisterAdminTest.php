<?php

namespace Tests\Feature\Auth;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.jwt_secret' => 'testing-secret']);
    }

    public function test_admin_can_register(): void
    {
        $response = $this->postJson('/api/admin/register', [
            'nama' => 'Admin Baru',
            'email' => 'baru@example.com',
            'password' => 'secret123',
            'confirm_password' => 'secret123',
        ]);

        $response
            ->assertStatus(201)
            ->assertJson([
                'message' => 'registrasi admin berhasil',
                'data' => [
                    'admin' => [
                        'nama' => 'Admin Baru',
                        'email' => 'baru@example.com',
                    ],
                ],
            ])
            ->assertJsonMissingPath('data.token');

        $this->assertDatabaseHas('admins', [
            'email' => 'baru@example.com',
        ]);
        $this->assertDatabaseCount('sessions', 0);
    }

    public function test_admin_register_requires_matching_passwords(): void
    {
        $response = $this->postJson('/api/admin/register', [
            'nama' => 'Admin Baru',
            'email' => 'baru@example.com',
            'password' => 'secret123',
            'confirm_password' => 'different',
        ]);

        $response->assertStatus(422);
    }
}

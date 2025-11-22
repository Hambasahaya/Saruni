<?php

namespace Database\Factories;

use App\Models\PasswordResetToken;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PasswordResetToken>
 */
class PasswordResetTokenFactory extends Factory
{
    protected $model = PasswordResetToken::class;

    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'user_id' => $this->faker->numberBetween(1, 1000),
            'role' => $this->faker->randomElement(['admin', 'guru', 'wali_kelas', 'siswa']),
            'token_hash' => bcrypt(Str::random(10)),
            'expires_at' => now()->addMinutes(15),
            'used_at' => null,
        ];
    }
}

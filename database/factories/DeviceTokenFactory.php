<?php

namespace Database\Factories;

use App\Models\DeviceToken;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeviceToken>
 */
class DeviceTokenFactory extends Factory
{
    protected $model = DeviceToken::class;

    public function definition(): array
    {
        return [
            'user_id' => $this->faker->numberBetween(1, 50),
            'token' => $this->faker->uuid(),
            'platform' => $this->faker->randomElement(['ios', 'android', 'web']),
        ];
    }
}

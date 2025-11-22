<?php

namespace Database\Factories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'body' => $this->faker->sentence(10),
            'type' => $this->faker->randomElement(['todo', 'absensi', 'pengumuman']),
            'payload' => ['foo' => 'bar'],
            'recipient' => $this->faker->numberBetween(1, 100),
            'read' => false,
        ];
    }
}

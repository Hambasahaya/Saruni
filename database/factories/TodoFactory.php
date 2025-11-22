<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\Todo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Todo>
 */
class TodoFactory extends Factory
{
    protected $model = Todo::class;

    public function definition(): array
    {
        $role = $this->faker->randomElement(['admin', 'guru', 'wali_kelas']);

        return [
            'admin_id' => $role === 'admin' ? Admin::factory() : null,
            'guru_id' => $role === 'guru' ? Guru::factory() : null,
            'wali_kelas_id' => $role === 'wali_kelas' ? Guru::factory() : null,
            'role' => $role,
            'tanggal' => $this->faker->date(),
            'deskripsi' => $this->faker->sentence(),
            'is_done' => $this->faker->boolean(),
            'jam_dibuat' => $this->faker->time(),
        ];
    }
}

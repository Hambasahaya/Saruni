<?php

namespace Database\Factories;

use App\Models\Kelas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kelas>
 */
class KelasFactory extends Factory
{
    protected $model = Kelas::class;

    public function definition(): array
    {
        $tingkatOptions = ['SD', 'SMP', 'SMA'];

        return [
            'nama' => 'Kelas ' . $this->faker->randomElement(['A', 'B', 'C']) . $this->faker->numberBetween(1, 9),
            'tingkat' => $this->faker->randomElement($tingkatOptions),
            'tahun_ajaran' => '2024/2025',
            'wali_kelas_id' => null,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\MataPelajaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MataPelajaran>
 */
class MataPelajaranFactory extends Factory
{
    protected $model = MataPelajaran::class;

    public function definition(): array
    {
        $tingkatOptions = ['SD', 'SMP', 'SMA'];
        $semesterOptions = ['ganjil', 'genap'];
        $hariOptions = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        return [
            'nama' => 'Mapel ' . $this->faker->unique()->word(),
            'kode' => strtoupper($this->faker->unique()->bothify('MP###')),
            'tingkat' => $this->faker->randomElement($tingkatOptions),
            'semester' => $this->faker->randomElement($semesterOptions),
            'hari' => $this->faker->randomElement($hariOptions),
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '09:00:00',
            'is_active' => true,
        ];
    }
}

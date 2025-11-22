<?php

namespace Database\Factories;

use App\Models\AbsensiSiswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AbsensiSiswa>
 */
class AbsensiSiswaFactory extends Factory
{
    protected $model = AbsensiSiswa::class;

    public function definition(): array
    {
        return [
            'siswa_id' => Siswa::factory(),
            'kelas_id' => Kelas::factory(),
            'mapel_id' => MataPelajaran::factory(),
            'guru_id' => Guru::factory(),
            'tipe_absensi' => $this->faker->randomElement(['kelas', 'mapel']),
            'tanggal' => $this->faker->date(),
            'status' => $this->faker->randomElement(['masuk', 'izin', 'sakit', 'terlambat', 'alpa']),
            'keterangan' => $this->faker->optional()->sentence(),
            'tahun_ajaran' => '2024/2025',
            'semester' => $this->faker->randomElement(['ganjil', 'genap']),
        ];
    }
}

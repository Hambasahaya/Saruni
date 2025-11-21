<?php

namespace Tests\Unit;

use App\Models\Guru;
use App\Models\GuruMapelKelas;
use App\Models\GuruRole;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Services\GuruRoleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuruRoleServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_wali_kelas_mapel_sets_existing_assignment(): void
    {
        $guru = Guru::factory()->create();
        $kelas = Kelas::factory()->create();
        $mapel = MataPelajaran::factory()->create();

        GuruMapelKelas::create([
            'guru_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'tahun_ajaran' => '2024/2025',
            'semester' => 'ganjil',
        ]);

        $role = GuruRole::create([
            'guru_id' => $guru->id,
            'role' => 'wali_kelas',
            'kelas_id' => $kelas->id,
        ]);

        app(GuruRoleService::class)->syncWaliKelasMapel($guru->id, $kelas->id);

        $this->assertEquals($mapel->id, $role->fresh()->mapel_id);
    }

    public function test_sync_wali_kelas_mapel_clears_when_assignment_missing(): void
    {
        $guru = Guru::factory()->create();
        $kelas = Kelas::factory()->create();

        $mapel = MataPelajaran::factory()->create();

        $role = GuruRole::create([
            'guru_id' => $guru->id,
            'role' => 'wali_kelas',
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
        ]);

        app(GuruRoleService::class)->syncWaliKelasMapel($guru->id, $kelas->id);

        $this->assertNull($role->fresh()->mapel_id);
    }
}

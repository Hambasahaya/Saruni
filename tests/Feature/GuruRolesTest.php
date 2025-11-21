<?php

namespace Tests\Feature;

use App\Http\Controllers\GuruController;
use App\Models\Guru;
use App\Models\GuruMapelKelas;
use App\Models\GuruRole;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Services\GuruRoleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuruRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_listing_includes_role_details(): void
    {
        $guru = Guru::factory()->create(['nama' => 'Pak Budi']);
        $kelas = Kelas::factory()->create([
            'nama' => 'VII A',
            'tingkat' => 'SMP',
            'wali_kelas_id' => $guru->id,
        ]);
        $mapel = MataPelajaran::factory()->create([
            'nama' => 'Matematika',
            'kode' => 'MATH1',
        ]);

        GuruMapelKelas::create([
            'guru_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'tahun_ajaran' => '2024/2025',
            'semester' => 'ganjil',
        ]);

        GuruRole::create([
            'guru_id' => $guru->id,
            'role' => 'wali_kelas',
            'kelas_id' => $kelas->id,
        ]);

        GuruRole::create([
            'guru_id' => $guru->id,
            'role' => 'guru_mapel',
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
        ]);

        app(GuruRoleService::class)->syncWaliKelasMapel($guru->id, $kelas->id);

        $response = app(GuruController::class)->index();
        $payload = $response->getData(true);

        $this->assertNotEmpty($payload['data']);

        $guruPayload = collect($payload['data'])->firstWhere('id', $guru->id);

        $this->assertIsArray($guruPayload['roles']);
        $this->assertContains('Guru', $guruPayload['roles']);
        $this->assertContains('Wali Kelas ' . $kelas->nama, $guruPayload['roles']);
        $this->assertContains(sprintf('Guru Mapel %s (%s)', $mapel->nama, $kelas->nama), $guruPayload['roles']);

        $roleDetail = collect($guruPayload['roles_detail'])
            ->firstWhere('role', 'guru_mapel');

        $this->assertNotNull($roleDetail);
        $this->assertEquals($mapel->id, $roleDetail['mapel']['id']);
        $this->assertEquals($kelas->id, $roleDetail['kelas']['id']);

        $waliDetail = collect($guruPayload['roles_detail'])
            ->firstWhere('role', 'wali_kelas');

        $this->assertNotNull($waliDetail);
        $this->assertEquals($mapel->id, $waliDetail['mapel']['id']);
    }
}

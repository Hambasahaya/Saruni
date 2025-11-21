<?php

namespace App\Services;

use App\Models\GuruMapelKelas;
use App\Models\GuruRole;

class GuruRoleService
{
    public function syncWaliKelasMapel(int $guruId, int $kelasId): void
    {
        $waliRole = GuruRole::where('guru_id', $guruId)
            ->where('role', 'wali_kelas')
            ->where('kelas_id', $kelasId)
            ->first();

        if (!$waliRole) {
            return;
        }

        $mapelId = GuruMapelKelas::where('guru_id', $guruId)
            ->where('kelas_id', $kelasId)
            ->latest('updated_at')
            ->value('mapel_id');

        $waliRole->mapel_id = $mapelId;
        $waliRole->save();
    }
}

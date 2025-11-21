<?php

namespace App\Http\Controllers;

use App\Models\GuruMapelKelas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PengajaranController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $role = (string) $request->attributes->get('role');
        $guruId = null;

        if ($role === 'guru' || $role === 'wali_kelas') {
            $guruId = (int) $request->attributes->get('user_id');
        } elseif ($role === 'admin') {
            $guruId = (int) $request->query('guru_id');
            if ($guruId === 0) {
                return $this->error('admin harus menyertakan query param guru_id', Response::HTTP_BAD_REQUEST);
            }
        } else {
            return $this->error('akses ditolak', Response::HTTP_FORBIDDEN);
        }

        $tahunAjaran = $request->query('tahun_ajaran', $this->currentAcademicYear());
        $semester = $request->query('semester', $this->currentSemester());

        $rows = GuruMapelKelas::with(['mataPelajaran', 'kelas'])
            ->where('guru_id', $guruId)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->orderBy('kelas_id')
            ->orderBy('mapel_id')
            ->get()
            ->map(function ($assignment) {
                return [
                    'mapel_id' => $assignment->mapel_id,
                    'mapel_nama' => $assignment->mataPelajaran?->nama,
                    'kelas_id' => $assignment->kelas_id,
                    'kelas_nama' => $assignment->kelas?->nama,
                    'tahun_ajaran' => $assignment->tahun_ajaran,
                    'semester' => $assignment->semester,
                ];
            });

        return response()->json([
            'message' => 'Daftar mapel & kelas yang diajar',
            'data' => $rows,
        ]);
    }

    private function currentAcademicYear(): string
    {
        $now = now();
        $startYear = $now->month >= 7 ? $now->year : $now->year - 1;

        return sprintf('%d/%d', $startYear, $startYear + 1);
    }

    private function currentSemester(): string
    {
        return now()->month >= 7 ? 'ganjil' : 'genap';
    }

    private function error(string $message, int $status): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], $status);
    }
}

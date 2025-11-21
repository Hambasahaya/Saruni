<?php

namespace App\Http\Controllers;

use App\Http\Requests\Guru\AssignWaliKelasRequest;
use App\Http\Requests\Guru\UnassignWaliKelasRequest;
use App\Models\Admin;
use App\Models\GuruRole;
use App\Models\Kelas;
use App\Services\GuruRoleService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class WaliKelasController extends Controller
{
    public function __construct(
        private NotificationService $notifications,
        private GuruRoleService $guruRoles,
    ) {}

    public function index(): JsonResponse
    {
        $kelas = Kelas::with('waliKelas')->get();

        $result = $kelas->filter(fn($item) => $item->waliKelas !== null)
            ->map(fn($item) => [
                'kelas_id' => $item->id,
                'nama_kelas' => $item->nama,
                'tingkat' => $item->tingkat,
                'tahun_ajaran' => $item->tahun_ajaran,
                'wali_kelas_id' => $item->waliKelas?->id,
                'wali_kelas_nama' => $item->waliKelas?->nama,
            ])->values();

        return $this->success('Berhasil mengambil data wali kelas', $result);
    }

    public function assign(AssignWaliKelasRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (GuruRole::where('guru_id', $data['guru_id'])->where('role', 'wali_kelas')->exists()) {
            abort(Response::HTTP_CONFLICT, 'Guru sudah menjadi wali kelas di kelas lain.');
        }

        $kelas = DB::transaction(function () use ($data) {
            $kelas = Kelas::lockForUpdate()->findOrFail($data['kelas_id']);
            $kelas->wali_kelas_id = $data['guru_id'];
            $kelas->save();

            GuruRole::updateOrCreate([
                'guru_id' => $data['guru_id'],
                'role' => 'wali_kelas',
                'kelas_id' => $data['kelas_id'],
            ], []);

            $this->guruRoles->syncWaliKelasMapel($data['guru_id'], $data['kelas_id']);

            return $kelas->load('waliKelas');
        });

        $recipients = array_merge(
            [$kelas->wali_kelas_id],
            Admin::pluck('id')->map(fn($id) => (int) $id)->all()
        );

        $this->notifications->notify(
            'assign_wali_kelas',
            'Penetapan Wali Kelas',
            sprintf('Guru %s ditetapkan sebagai wali kelas %s.', $kelas->waliKelas?->nama, $kelas->nama),
            [
                'guru_id' => $kelas->wali_kelas_id,
                'kelas_id' => $kelas->id,
                'kelas_nama' => $kelas->nama,
            ],
            array_values(array_unique(array_filter($recipients)))
        );

        return $this->success('Wali kelas berhasil ditetapkan', $kelas);
    }

    public function unassign(UnassignWaliKelasRequest $request): JsonResponse
    {
        $data = $request->validated();

        $kelas = Kelas::with('waliKelas')->findOrFail($data['kelas_id']);

        if (!$kelas->wali_kelas_id) {
            abort(Response::HTTP_BAD_REQUEST, 'Kelas tersebut belum memiliki wali kelas');
        }

        $prevWaliId = $kelas->wali_kelas_id;
        $prevWaliNama = $kelas->waliKelas?->nama;

        DB::transaction(function () use ($kelas, $prevWaliId) {
            $kelas->wali_kelas_id = null;
            $kelas->save();

            GuruRole::where('guru_id', $prevWaliId)
                ->where('role', 'wali_kelas')
                ->where('kelas_id', $kelas->id)
                ->delete();
        });

        $this->notifications->notify(
            'unassign_wali_kelas',
            'Pencabutan Wali Kelas',
            sprintf(
                'Penetapan wali kelas untuk kelas %s dibatalkan. Guru %s tidak lagi menjabat.',
                $kelas->nama,
                $prevWaliNama
            ),
            [
                'guru_id' => $prevWaliId,
                'kelas_id' => $kelas->id,
                'kelas_nama' => $kelas->nama,
            ],
            array_values(array_unique(array_filter(array_merge([$prevWaliId], Admin::pluck('id')->toArray()))))
        );

        return $this->success('Wali kelas berhasil dihapus dari kelas', $kelas->fresh('waliKelas'));
    }

    private function success(string $message, mixed $data = null, int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $status);
    }
}

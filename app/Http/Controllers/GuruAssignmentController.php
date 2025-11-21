<?php

namespace App\Http\Controllers;

use App\Http\Requests\Guru\AssignMapelKelasRequest;
use App\Models\Admin;
use App\Models\GuruMapelKelas;
use App\Models\GuruRole;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Services\GuruRoleService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class GuruAssignmentController extends Controller
{
    public function __construct(
        private NotificationService $notifications,
        private GuruRoleService $guruRoles,
    ) {}

    public function index(): JsonResponse
    {
        $assignments = GuruMapelKelas::with(['guru', 'mataPelajaran', 'kelas'])->get();

        return $this->success('Daftar penugasan guru ke mapel dan kelas', $assignments);
    }

    public function store(AssignMapelKelasRequest $request): JsonResponse
    {
        $data = $request->validated();

        $assignment = DB::transaction(function () use ($data) {
            $conflict = GuruMapelKelas::where('kelas_id', $data['kelas_id'])
                ->where('mapel_id', $data['mapel_id'])
                ->where('tahun_ajaran', $data['tahun_ajaran'])
                ->where('semester', $data['semester'])
                ->first();

            if ($conflict) {
                abort(Response::HTTP_CONFLICT, 'Kombinasi kelas dan mapel pada periode ini sudah ditugaskan.');
            }

            $existing = GuruMapelKelas::where('guru_id', $data['guru_id'])
                ->where('mapel_id', $data['mapel_id'])
                ->where('kelas_id', $data['kelas_id'])
                ->where('tahun_ajaran', $data['tahun_ajaran'])
                ->where('semester', $data['semester'])
                ->first();

            if ($existing) {
                abort(Response::HTTP_CONFLICT, 'Guru sudah mengajar mapel ini di kelas dan periode tersebut.');
            }

            $assignment = GuruMapelKelas::create($data);

            GuruRole::firstOrCreate([
                'guru_id' => $data['guru_id'],
                'role' => 'guru_mapel',
                'kelas_id' => $data['kelas_id'],
                'mapel_id' => $data['mapel_id'],
            ]);

            $this->guruRoles->syncWaliKelasMapel($data['guru_id'], $data['kelas_id']);

            return $assignment->load(['guru', 'mataPelajaran', 'kelas']);
        });

        $recipientIds = array_merge(
            [$assignment->guru_id],
            Kelas::whereKey($assignment->kelas_id)
                ->whereNotNull('wali_kelas_id')
                ->pluck('wali_kelas_id')
                ->map(fn($id) => (int) $id)
                ->all(),
            Admin::pluck('id')->map(fn($id) => (int) $id)->all()
        );

        $this->notifications->notify(
            'assign_guru_mapel',
            'Penugasan Guru ke Mata Pelajaran',
            sprintf(
                'Guru %s ditugaskan mengajar %s di kelas %s Tahun Ajaran %s (%s).',
                $assignment->guru->nama,
                $assignment->mataPelajaran->nama,
                $assignment->kelas->nama,
                $assignment->tahun_ajaran,
                $assignment->semester
            ),
            [
                'guru_id' => $assignment->guru_id,
                'mapel_id' => $assignment->mapel_id,
                'kelas_id' => $assignment->kelas_id,
                'tahun_ajaran' => $assignment->tahun_ajaran,
                'semester' => $assignment->semester,
            ],
            array_values(array_unique(array_filter($recipientIds)))
        );

        return $this->success('Guru berhasil ditetapkan ke mapel dan kelas', $assignment, Response::HTTP_CREATED);
    }

    public function update(AssignMapelKelasRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();

        $assignment = GuruMapelKelas::with(['guru', 'mataPelajaran', 'kelas'])->findOrFail($id);

        DB::transaction(function () use ($assignment, $data) {
            $conflict = GuruMapelKelas::where('kelas_id', $data['kelas_id'])
                ->where('mapel_id', $data['mapel_id'])
                ->where('tahun_ajaran', $data['tahun_ajaran'])
                ->where('semester', $data['semester'])
                ->where('id', '<>', $assignment->id)
                ->first();

            if ($conflict) {
                abort(Response::HTTP_CONFLICT, 'Kombinasi kelas+mapel+periode sudah ditugaskan ke guru lain');
            }

            $old = $assignment->replicate();

            $assignment->update($data);

            GuruRole::where('guru_id', $old->guru_id)
                ->where('role', 'guru_mapel')
                ->where('kelas_id', $old->kelas_id)
                ->where('mapel_id', $old->mapel_id)
                ->delete();

            GuruRole::firstOrCreate([
                'guru_id' => $assignment->guru_id,
                'role' => 'guru_mapel',
                'kelas_id' => $assignment->kelas_id,
                'mapel_id' => $assignment->mapel_id,
            ]);

            $this->guruRoles->syncWaliKelasMapel($old->guru_id, $old->kelas_id);
            $this->guruRoles->syncWaliKelasMapel($assignment->guru_id, $assignment->kelas_id);
        });

        return $this->success('Penugasan berhasil diperbarui', $assignment->fresh(['guru', 'mataPelajaran', 'kelas']));
    }

    public function destroy(int $id): JsonResponse
    {
        $assignment = GuruMapelKelas::with(['guru', 'mataPelajaran', 'kelas'])->findOrFail($id);

        DB::transaction(function () use ($assignment) {
            GuruRole::where('guru_id', $assignment->guru_id)
                ->where('role', 'guru_mapel')
                ->where('kelas_id', $assignment->kelas_id)
                ->where('mapel_id', $assignment->mapel_id)
                ->delete();

            $assignment->delete();

            $this->guruRoles->syncWaliKelasMapel($assignment->guru_id, $assignment->kelas_id);
        });

        $this->notifications->notify(
            'delete_guru_mapel',
            'Penghapusan Penugasan Guru',
            sprintf(
                'Penugasan Guru %s untuk %s di kelas %s pada Tahun Ajaran %s (%s) dihapus.',
                $assignment->guru->nama,
                $assignment->mataPelajaran->nama,
                $assignment->kelas->nama,
                $assignment->tahun_ajaran,
                $assignment->semester
            ),
            [
                'guru_id' => $assignment->guru_id,
                'mapel_id' => $assignment->mapel_id,
                'kelas_id' => $assignment->kelas_id,
                'tahun_ajaran' => $assignment->tahun_ajaran,
                'semester' => $assignment->semester,
            ],
        );

        return $this->success('Penugasan berhasil dihapus');
    }

    private function success(string $message, mixed $data = null, int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $status);
    }
}

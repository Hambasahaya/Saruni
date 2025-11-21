<?php

namespace App\Http\Controllers;

use App\Http\Requests\Guru\StoreGuruRequest;
use App\Http\Requests\Guru\UpdateGuruRequest;
use App\Models\Admin;
use App\Models\Guru;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class GuruController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    public function index(): JsonResponse
    {
        $gurus = Guru::with(['roles.kelas', 'roles.mapel'])
            ->orderBy('nama')
            ->get()
            ->map(fn(Guru $guru) => $this->transformGuru($guru));

        return $this->success('Daftar guru berhasil diambil', $gurus);
    }

    public function show(int $id): JsonResponse
    {
        $guru = Guru::with(['roles.kelas', 'roles.mapel'])->findOrFail($id);

        return $this->success('Berhasil mengambil data guru', $this->transformGuru($guru));
    }

    public function store(StoreGuruRequest $request): JsonResponse
    {
        $data = $request->validated();

        $guru = DB::transaction(function () use ($data) {
            return Guru::create([
                'nama' => $data['nama'],
                'nip' => $data['nip'],
                'nik' => $data['nik'],
                'email' => $data['email'],
                'telepon' => $data['telepon'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'jenis_kelamin' => $data['jenis_kelamin'],
                'password' => Hash::make($data['nip']),
            ]);
        });

        $recipientIds = Admin::query()->pluck('id')->map(fn($id) => (int) $id)->all();
        $recipientIds[] = $guru->id;

        $this->notifications->notify(
            'create_guru',
            'Akun Guru Baru Dibuat',
            sprintf('Akun guru %s (%s) berhasil dibuat.', $guru->nama, $guru->email),
            [
                'guru_id' => $guru->id,
                'nama' => $guru->nama,
                'email' => $guru->email,
            ],
            array_values(array_unique(array_filter($recipientIds)))
        );

        return $this->success(
            'Guru berhasil dibuat',
            ['guru' => $this->transformGuru($guru->fresh(['roles.kelas', 'roles.mapel']))],
            Response::HTTP_CREATED
        );
    }

    public function update(UpdateGuruRequest $request, int $id): JsonResponse
    {
        $guru = Guru::findOrFail($id);
        $data = $request->validated();

        if (!empty($data)) {
            $guru->fill($data);
            $guru->save();
        }

        return $this->success('Berhasil memperbarui data guru', $this->transformGuru($guru->fresh(['roles.kelas', 'roles.mapel'])));
    }

    public function destroy(int $id): JsonResponse
    {
        $guru = Guru::findOrFail($id);
        $guru->delete();

        return $this->success('Berhasil menghapus data guru');
    }

    private function success(string $message, mixed $data = null, int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    private function transformGuru(Guru $guru): array
    {
        $guru->loadMissing(['roles.kelas', 'roles.mapel']);

        $payload = $guru->toArray();
        unset($payload['roles']);

        $roleDetails = $this->buildRoleDetails($guru);

        $payload['roles_detail'] = $roleDetails;
        $payload['roles'] = $this->summarizeRoles($roleDetails);

        return $payload;
    }

    private function buildRoleDetails(Guru $guru): array
    {
        $details = [[
            'role' => 'guru',
            'label' => 'Guru',
            'kelas' => null,
            'mapel' => null,
        ]];

        foreach ($guru->roles as $role) {
            $details[] = [
                'role' => $role->role,
                'label' => $role->role === 'wali_kelas' ? 'Wali Kelas' : 'Guru Mapel',
                'kelas' => $role->kelas ? [
                    'id' => $role->kelas->id,
                    'nama' => $role->kelas->nama,
                    'tingkat' => $role->kelas->tingkat,
                    'tahun_ajaran' => $role->kelas->tahun_ajaran,
                ] : null,
                'mapel' => $role->mapel ? [
                    'id' => $role->mapel->id,
                    'nama' => $role->mapel->nama,
                    'kode' => $role->mapel->kode,
                ] : null,
            ];
        }

        return $details;
    }

    private function summarizeRoles(array $details): array
    {
        return collect($details)
            ->map(function (array $detail): string {
                return match ($detail['role']) {
                    'guru' => 'Guru',
                    'wali_kelas' => $detail['kelas']
                        ? sprintf('Wali Kelas %s', $detail['kelas']['nama'])
                        : 'Wali Kelas',
                    'guru_mapel' => ($detail['mapel'] && $detail['kelas'])
                        ? sprintf('Guru Mapel %s (%s)', $detail['mapel']['nama'], $detail['kelas']['nama'])
                        : 'Guru Mapel',
                    default => ucfirst(str_replace('_', ' ', (string) $detail['role'])),
                };
            })
            ->unique()
            ->values()
            ->all();
    }
}

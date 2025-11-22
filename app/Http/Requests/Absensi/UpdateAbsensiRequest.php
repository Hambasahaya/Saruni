<?php

namespace App\Http\Requests\Absensi;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAbsensiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'siswa_id' => ['sometimes', 'exists:siswas,id'],
            'kelas_id' => ['sometimes', 'exists:kelas,id'],
            'mapel_id' => ['sometimes', 'nullable', 'exists:mata_pelajarans,id'],
            'guru_id' => ['sometimes', 'nullable', 'exists:gurus,id'],
            'tipe_absensi' => ['sometimes', 'in:kelas,mapel'],
            'tanggal' => ['sometimes', 'date'],
            'status' => ['sometimes', 'in:masuk,izin,sakit,terlambat,alpa'],
            'keterangan' => ['sometimes', 'nullable', 'string'],
            'tahun_ajaran' => ['sometimes', 'regex:/^\d{4}\/\d{4}$/'],
            'semester' => ['sometimes', 'in:ganjil,genap'],
        ];
    }
}

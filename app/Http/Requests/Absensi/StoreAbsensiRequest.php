<?php

namespace App\Http\Requests\Absensi;

use Illuminate\Foundation\Http\FormRequest;

class StoreAbsensiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'siswa_id' => ['required', 'exists:siswas,id'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'mapel_id' => ['nullable', 'exists:mata_pelajarans,id'],
            'guru_id' => ['nullable', 'exists:gurus,id'],
            'tipe_absensi' => ['required', 'in:kelas,mapel'],
            'tanggal' => ['required', 'date'],
            'status' => ['required', 'in:masuk,izin,sakit,terlambat,alpa'],
            'keterangan' => ['nullable', 'string'],
            'tahun_ajaran' => ['required', 'regex:/^\d{4}\/\d{4}$/'],
            'semester' => ['required', 'in:ganjil,genap'],
        ];
    }
}

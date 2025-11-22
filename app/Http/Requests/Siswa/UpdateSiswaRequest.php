<?php

namespace App\Http\Requests\Siswa;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $siswaId = $this->route('id');

        return [
            'nama' => ['sometimes', 'string', 'max:100'],
            'nisn' => ['sometimes', 'string', 'max:20', Rule::unique('siswas', 'nisn')->ignore($siswaId)],
            'tempat_lahir' => ['sometimes', 'string', 'max:100'],
            'tanggal_lahir' => ['sometimes', 'date'],
            'jenis_kelamin' => ['sometimes', 'in:L,P'],
            'nama_ayah' => ['sometimes', 'string', 'max:100'],
            'nama_ibu' => ['sometimes', 'string', 'max:100'],
            'alamat' => ['sometimes', 'string'],
            'agama' => ['sometimes', 'string', 'max:20'],
            'email' => ['sometimes', 'email', 'max:100', Rule::unique('siswas', 'email')->ignore($siswaId)],
            'telepon' => ['sometimes', 'string', 'max:20'],
            'asal_sekolah' => ['sometimes', 'string', 'max:100'],
            'password' => ['sometimes', 'string', 'min:6'],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
        ];
    }
}

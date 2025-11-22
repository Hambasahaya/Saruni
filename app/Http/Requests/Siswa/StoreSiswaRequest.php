<?php

namespace App\Http\Requests\Siswa;

use Illuminate\Foundation\Http\FormRequest;

class StoreSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            'nisn' => ['required', 'string', 'max:20', 'unique:siswas,nisn'],
            'tempat_lahir' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'nama_ayah' => ['required', 'string', 'max:100'],
            'nama_ibu' => ['required', 'string', 'max:100'],
            'alamat' => ['required', 'string'],
            'agama' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100', 'unique:siswas,email'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'asal_sekolah' => ['required', 'string', 'max:100'],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
        ];
    }
}

<?php

namespace App\Http\Requests\Siswa;

use Illuminate\Foundation\Http\FormRequest;

class AssignSiswaKelasRequest extends FormRequest
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
        ];
    }
}

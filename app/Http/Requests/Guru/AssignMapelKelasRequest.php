<?php

namespace App\Http\Requests\Guru;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignMapelKelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'guru_id' => ['required', 'integer', 'exists:gurus,id'],
            'mapel_id' => ['required', 'integer', 'exists:mata_pelajarans,id'],
            'kelas_id' => ['required', 'integer', 'exists:kelas,id'],
            'tahun_ajaran' => ['required', 'string', 'max:9'],
            'semester' => ['required', Rule::in(['ganjil', 'genap'])],
        ];
    }
}

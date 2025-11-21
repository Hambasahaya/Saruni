<?php

namespace App\Http\Requests\Guru;

use Illuminate\Foundation\Http\FormRequest;

class AssignWaliKelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'guru_id' => ['required', 'integer', 'exists:gurus,id'],
            'kelas_id' => ['required', 'integer', 'exists:kelas,id'],
        ];
    }
}

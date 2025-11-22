<?php

namespace App\Http\Requests\Kelas;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['sometimes', 'string', 'max:50'],
            'tingkat' => ['sometimes', 'in:SD,SMP,SMA'],
            'tahun_ajaran' => ['sometimes', 'regex:/^\d{4}\/\d{4}$/'],
            'wali_kelas_id' => ['nullable', 'exists:gurus,id'],
        ];
    }
}

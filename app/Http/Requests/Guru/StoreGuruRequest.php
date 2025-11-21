<?php

namespace App\Http\Requests\Guru;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGuruRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            'nip' => ['required', 'string', 'max:30', 'unique:gurus,nip'],
            'nik' => ['required', 'string', 'max:30', 'unique:gurus,nik'],
            'email' => ['required', 'email', 'max:100', 'unique:gurus,email'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
        ];
    }
}

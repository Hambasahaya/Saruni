<?php

namespace App\Http\Requests\Guru;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGuruRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $guruId = $this->route('id') ?? $this->route('guru');

        return [
            'nama' => ['sometimes', 'required', 'string', 'max:100'],
            'nip' => ['sometimes', 'required', 'string', 'max:30', Rule::unique('gurus', 'nip')->ignore($guruId)],
            'nik' => ['sometimes', 'required', 'string', 'max:30', Rule::unique('gurus', 'nik')->ignore($guruId)],
            'email' => ['sometimes', 'required', 'email', 'max:100', Rule::unique('gurus', 'email')->ignore($guruId)],
            'telepon' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'jenis_kelamin' => ['sometimes', 'required', Rule::in(['L', 'P'])],
        ];
    }
}

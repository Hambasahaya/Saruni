<?php

namespace App\Http\Requests\Todo;

use Illuminate\Foundation\Http\FormRequest;

class StoreTodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'deskripsi' => ['required', 'string'],
            'tanggal' => ['required', 'date'],
            'jam_dibuat' => ['nullable', 'date_format:H:i'],
            'role' => ['nullable', 'in:admin,guru,wali_kelas'],
            'target_id' => ['nullable', 'integer'],
        ];
    }
}

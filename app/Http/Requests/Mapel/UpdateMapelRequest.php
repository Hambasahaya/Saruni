<?php

namespace App\Http\Requests\Mapel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMapelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $mapelId = $this->route('id');

        return [
            'nama' => ['sometimes', 'string', 'max:100'],
            'kode' => ['sometimes', 'string', 'max:20', Rule::unique('mata_pelajarans', 'kode')->ignore($mapelId)],
            'tingkat' => ['sometimes', 'in:SD,SMP,SMA'],
            'semester' => ['sometimes', 'in:ganjil,genap'],
            'hari' => ['sometimes', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'],
            'jam_mulai' => ['sometimes', 'date_format:H:i'],
            'jam_selesai' => ['sometimes', 'date_format:H:i', 'after:jam_mulai'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}

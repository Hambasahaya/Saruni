<?php

namespace App\Http\Requests\Mapel;

use Illuminate\Foundation\Http\FormRequest;

class StoreMapelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            'kode' => ['required', 'string', 'max:20', 'unique:mata_pelajarans,kode'],
            'tingkat' => ['required', 'in:SD,SMP,SMA'],
            'semester' => ['required', 'in:ganjil,genap'],
            'hari' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'is_active' => ['boolean'],
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'gurus';

    protected $fillable = [
        'nama',
        'nip',
        'nik',
        'email',
        'telepon',
        'alamat',
        'jenis_kelamin',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    public function roles(): HasMany
    {
        return $this->hasMany(GuruRole::class);
    }
}

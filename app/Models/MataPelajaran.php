<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataPelajaran extends Model
{
    use HasFactory;

    protected $table = 'mata_pelajarans';

    protected $fillable = [
        'nama',
        'kode',
        'tingkat',
        'semester',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(GuruMapelKelas::class, 'mapel_id');
    }

    public function siswas(): BelongsToMany
    {
        return $this->belongsToMany(Siswa::class, 'mapel_siswas', 'mata_pelajaran_id', 'siswa_id');
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(AbsensiSiswa::class, 'mapel_id');
    }
}

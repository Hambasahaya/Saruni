<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswas';

    protected $fillable = [
        'nama',
        'nisn',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'nama_ayah',
        'nama_ibu',
        'alamat',
        'agama',
        'email',
        'telepon',
        'asal_sekolah',
        'password',
        'kelas_id',
    ];

    protected $hidden = [
        'password',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mataPelajarans(): BelongsToMany
    {
        return $this->belongsToMany(MataPelajaran::class, 'mapel_siswas', 'siswa_id', 'mata_pelajaran_id');
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(AbsensiSiswa::class);
    }
}

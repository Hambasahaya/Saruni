<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuruRole extends Model
{
    use HasFactory;

    protected $table = 'guru_roles';

    protected $fillable = [
        'guru_id',
        'role',
        'kelas_id',
        'mapel_id',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }
}

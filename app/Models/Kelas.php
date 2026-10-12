<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'nama', 'tingkat', 'jurusan', 'wali_kelas_id',
    ];

    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'wali_kelas_id');
    }

    public function jurnalHarian(): HasMany
    {
        return $this->hasMany(JurnalHarian::class, 'id_kelas');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'kelas_id');
    }
}
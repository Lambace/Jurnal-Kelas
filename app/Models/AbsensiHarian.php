<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiHarian extends Model
{
    use HasFactory;

    protected $table = 'absensi_harian';

    protected $fillable = [
        'jurnal_id', 'user_id', 'kelas_id', 'tanggal',
        'status', 'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function jurnal(): BelongsTo { return $this->belongsTo(JurnalHarian::class, 'jurnal_id'); }
    public function siswa(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function kelas(): BelongsTo { return $this->belongsTo(Kelas::class, 'kelas_id'); }

    public function getColorClass(): string
    {
        return match($this->status) {
            'hadir' => 'bg-green-100 text-green-700',
            'sakit' => 'bg-yellow-100 text-yellow-700',
            'izin' => 'bg-blue-100 text-blue-700',
            'alpha' => 'bg-red-100 text-red-700',
            'terlambat' => 'bg-orange-100 text-orange-700',
            'bolos' => 'bg-purple-100 text-purple-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}
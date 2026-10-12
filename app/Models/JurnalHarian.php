<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JurnalHarian extends Model
{
    use HasFactory;

    protected $table = 'jurnal_harian';

    protected $fillable = [
        'tanggal', 'id_kelas', 'id_pengisi', 'peran_pengisi',
        'cuaca', 'kehadiran', 'terlambat', 'catatan_ketidakhadiran',
        'kebersihan', 'suhu_ruangan', 'catatan_fasilitas',
        'mata_pelajaran', 'topik_materi', 'metode_pembelajaran', 'hambatan_belajar',
        'kejadian_positif', 'pelanggaran', 'siswa_bermasalah', 'catatan_pribadi_guru',
        'status', 'id_penyetuju', 'disetujui_pada', 'catatan_penolakan',
        'guru_pengampu', 'catatan_wali_kelas',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'kehadiran' => 'array',
        'disetujui_pada' => 'datetime',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }

    public function pengisi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengisi');
    }

    public function penyetuju(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_penyetuju');
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(AbsensiHarian::class, 'jurnal_id');
    }

    public function sedangMenunggu(): bool { return $this->status === 'menunggu_verifikasi'; }
    public function sudahDisetujui(): bool { return $this->status === 'disetujui'; }
    public function sudahDitolak(): bool { return $this->status === 'ditolak'; }
}
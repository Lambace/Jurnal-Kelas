<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nama', 
        'email', 
        'password', 
        'peran',
        'provider', 
        'provider_id',
        'kelas_id', 
        'nama_ortu', 
        'no_wa_ortu',
        'nip', 
        'no_hp',
    ];

    protected $hidden = [
        'password', 
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ==========================================
    // AUTO-GENERATE EMAIL & PASSWORD DEFAULT
    // ==========================================

    /**
     * Buat email otomatis dari nama.
     * Contoh: "Aura Kasih" (siswa) -> aurakasih@siswa.id
     * Jika sudah dipakai, otomatis tambah angka: aurakasih2@siswa.id
     */
    public static function generateEmail(string $nama, string $peran = 'siswa', ?int $exceptId = null): string
    {
        $domain = match ($peran) {
            'siswa'              => 'siswa.id',
            'guru', 'wali_kelas' => 'guru.id',
            default              => 'sekolah.id',
        };

        // ambil huruf saja, lowercase, tanpa spasi & simbol
        $base = preg_replace('/[^a-z]/', '', strtolower($nama)) ?: 'user';

        $email = $base . '@' . $domain;
        $i = 2;

        while (static::where('email', $email)
                    ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
                    ->exists()) {
            $email = $base . $i . '@' . $domain;
            $i++;
        }

        return $email;
    }

    /** Password default untuk semua user baru */
    public static function defaultPassword(): string
    {
        return 'password';
    }

    // ==========================================
    // RELASI (RELATIONSHIPS)
    // ==========================================

    /**
     * Kelas tempat siswa ini berada (untuk role siswa)
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Kelas-kelas yang diampu oleh user ini (untuk role wali_kelas)
     */
    public function kelasWali(): HasMany
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id');
    }

    /**
     * Jurnal yang diisi oleh user ini
     */
    public function jurnalDiisi(): HasMany
    {
        return $this->hasMany(JurnalHarian::class, 'id_pengisi');
    }

    /**
     * Jurnal yang disetujui/diverifikasi oleh user ini
     */
    public function jurnalDisetujui(): HasMany
    {
        return $this->hasMany(JurnalHarian::class, 'id_penyetuju');
    }

    /**
     * Data absensi harian milik user ini
     */
    public function absensi(): HasMany
    {
        return $this->hasMany(AbsensiHarian::class, 'user_id');
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    public function isAdmin(): bool 
    { 
        return $this->peran === 'admin'; 
    }

    public function isWaliKelas(): bool 
    { 
        return $this->peran === 'wali_kelas'; 
    }

    public function isSiswa(): bool 
    { 
        return $this->peran === 'siswa'; 
    }
    
    public function isGuru(): bool 
    { 
        return $this->peran === 'guru'; 
    }


    public function kelasIdsDiampu()
    {
        if ($this->isAdmin()) {
            return Kelas::pluck('id');
        }

        if ($this->isWaliKelas()) {
            return $this->kelasWali()->pluck('id');
        }

        return collect();
    }
}
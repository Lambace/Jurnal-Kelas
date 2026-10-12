<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Kelas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnFailure
{
    /**
     * Proses setiap baris Excel menjadi model User.
     * Email & password di-generate otomatis dari nama siswa.
     */
    public function model(array $row): ?Model
    {
        // Skip baris tanpa nama (validasi tambahan di luar rules)
        if (empty($row['nama_lengkap'])) {
            return null;
        }

        // Cari kelas berdasarkan nama di Excel (nullable, jadi siswa tetap bisa dibuat walau kelas tidak ketemu)
        $kelas = Kelas::where('nama', $row['nama_kelas'] ?? null)->first();

        // ✨ Generate email otomatis dari nama (domain: @siswa.id)
        $email = User::generateEmail($row['nama_lengkap'], 'siswa');

        // ✨ Cari siswa lama berdasarkan nama + kelas (lebih aman daripada berdasarkan email)
        // Ini mencegah duplikasi jika guru mengimpor ulang data yang sama
        $existing = User::where('nama', $row['nama_lengkap'])
            ->where('kelas_id', $kelas?->id)
            ->first();

        if ($existing) {
            // Update data lama, TAPI jangan timpa password jika sudah pernah diganti
            $existing->update([
                'nama_ortu'  => $row['nama_orang_tua'] ?? $existing->nama_ortu,
                'no_wa_ortu' => $row['no_wa_orang_tua'] ?? $existing->no_wa_ortu,
                'kelas_id'   => $kelas?->id ?? $existing->kelas_id,
            ]);
            return $existing;
        }

        // Buat siswa baru dengan email & password otomatis
        return User::create([
            'nama'       => $row['nama_lengkap'],
            'email'      => $email,
            'password'   => Hash::make(User::defaultPassword()), // default: "password"
            'peran'      => 'siswa',
            'kelas_id'   => $kelas?->id,
            'nama_ortu'  => $row['nama_orang_tua'] ?? null,
            'no_wa_ortu' => $row['no_wa_orang_tua'] ?? null,
        ]);
    }

    /**
     * Aturan validasi untuk setiap baris Excel.
     * Email & password TIDAK diperlukan di Excel karena di-generate otomatis.
     */
    public function rules(): array
    {
        return [
            'nama_lengkap'    => 'required|string|max:255',
            'nama_kelas'      => 'nullable|string|max:255',
            'nama_orang_tua'  => 'nullable|string|max:255',
            'no_wa_orang_tua' => 'nullable|string|max:20',
        ];
    }

    /**
     * Pesan error custom untuk setiap field (lebih ramah user).
     */
    public function customValidationMessages(): array
    {
        return [
            'nama_lengkap.required' => 'Baris ke-:row: Kolom "Nama Lengkap" wajib diisi.',
            'nama_lengkap.string'   => 'Baris ke-:row: Kolom "Nama Lengkap" harus berupa teks.',
        ];
    }

    /**
     * Penanganan error validasi per baris (tidak menghentikan proses impor).
     */
    public function onFailure(Failure ...$failures): void
    {
        foreach ($failures as $failure) {
            // Catat baris yang gagal ke log agar bisa ditelusuri admin
            logger()->warning('Import siswa gagal', [
                'row'    => $failure->row(),
                'values' => $failure->values(),
                'errors' => $failure->errors(),
            ]);
        }
    }
}
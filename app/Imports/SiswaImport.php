<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Kelas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation
{
    // PERBAIKAN: Tambahkan return type ": ?Model"
    public function model(array $row): ?Model
    {
        // Skip baris yang tidak memiliki data wajib
        if (empty($row['nama_lengkap']) || empty($row['email'])) {
            return null;
        }

        // Cari kelas berdasarkan nama yang diinput di Excel
        $kelas = Kelas::where('nama', $row['nama_kelas'])->first();

        // Gunakan updateOrCreate agar jika email sudah ada, data akan di-update
        return User::updateOrCreate(
            ['email' => $row['email']], // Kondisi pencarian
            [
                'nama' => $row['nama_lengkap'],
                'password' => Hash::make($row['password_default_password123'] ?? 'password123'),
                'peran' => 'siswa',
                'kelas_id' => $kelas ? $kelas->id : null,
                'nama_ortu' => $row['nama_orang_tua'] ?? null,
                'no_wa_ortu' => $row['no_wa_orang_tua'] ?? null,
            ]
        );
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => 'required|string',
            'email' => 'required|email',
        ];
    }
}
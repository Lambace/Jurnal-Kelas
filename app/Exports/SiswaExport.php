<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SiswaExport implements FromCollection, WithHeadings, WithMapping
{
    // PERBAIKAN: Tambahkan return type ": Collection"
    public function collection(): Collection
    {
        // Hanya export user dengan peran 'siswa', sertakan relasi kelas
        return User::where('peran', 'siswa')->with('kelas')->get();
    }

    public function headings(): array
    {
        return [
            'Nama Lengkap',
            'Email',
            'Password (Default: password123)',
            'Nama Kelas',
            'Nama Orang Tua',
            'No WA Orang Tua',
        ];
    }

    public function map($user): array
    {
        return [
            $user->nama,
            $user->email,
            'password123', // Password default untuk keamanan
            $user->kelas ? $user->kelas->nama : 'Belum ada kelas',
            $user->nama_ortu ?? '-',
            $user->no_wa_ortu ?? '-',
        ];
    }
}
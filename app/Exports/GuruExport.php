<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GuruExport implements FromCollection, WithHeadings, WithMapping
{
    // PERBAIKAN: Tambahkan return type ": Collection"
    public function collection(): Collection
    {
        // Hanya export user dengan peran 'guru'
        return User::where('peran', 'guru')->get();
    }

    public function headings(): array
    {
        return [
            'Nama Lengkap',
            'NIP',
            'Email',
            'Password (Default: password123)',
            'No. HP / WhatsApp',
            'Peran',
        ];
    }

    public function map($user): array
    {
        return [
            $user->nama,
            $user->nip ?? '-',
            $user->email,
            'password123', // Password default untuk keamanan
            $user->no_hp ?? '-',
            'Peran',
        ];
    }
}
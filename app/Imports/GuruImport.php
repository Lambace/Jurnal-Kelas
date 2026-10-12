<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class GuruImport implements ToModel, WithHeadingRow, WithValidation
{
    // PERBAIKAN: Tambahkan return type ": ?Model"
        public function model(array $row): ?Model
    {
        if (empty($row['nama_lengkap']) || empty($row['email'])) {
            return null;
        }

        return User::updateOrCreate(
            ['email' => $row['email']],
            [
                'nama' => $row['nama_lengkap'],
                'nip' => $row['nip'] ?? null,
                'password' => Hash::make($row['password_default_password123'] ?? 'password123'),
                'peran' => $row['peran'] ?? 'guru', // ⚠️ TAMBAHKAN INI
                'no_hp' => $row['no_hp_whatsapp'] ?? null,
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

        public function model(array $row): ?Model
    {
        if (empty($row['nama_lengkap']) || empty($row['email'])) {
            return null;
        }

        
    }
}
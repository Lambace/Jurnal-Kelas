<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Admin
        User::create([
            'nama' => 'Admin Sekolah',
            'email' => 'admin@sekolah.sch.id',
            'password' => Hash::make('password'),
            'peran' => 'admin',
        ]);

     $guru1 = \App\Models\User::create([
       'nama' => 'Ibu Siti Rahayu, S.Pd',
       'email' => 'siti.guru@sekolah.sch.id',
       'password' => \Illuminate\Support\Facades\Hash::make('password'),
       'peran' => 'guru',
       'nip' => '198501012010012001', // Kita akan buat kolom ini di Fase 2
       'no_hp' => '081234567890',
   ]);

        // Buat Wali Kelas
        $waliKelas = User::create([
            'nama' => 'Pak Budi Santoso',
            'email' => 'budi@sekolah.sch.id',
            'password' => Hash::make('password'),
            'peran' => 'wali_kelas',
        ]);

        // Buat Siswa
        $siswa = User::create([
            'nama' => 'Ahmad Rizki',
            'email' => 'ahmad@sekolah.sch.id',
            'password' => Hash::make('password'),
            'peran' => 'siswa',
        ]);

        // Buat Kelas
        $kelas1 = Kelas::create([
            'nama' => 'XII RPL 1',
            'tingkat' => '12',
            'jurusan' => 'RPL',
            'wali_kelas_id' => $waliKelas->id,
        ]);

        Kelas::create([
            'nama' => 'XI TKJ 2',
            'tingkat' => '11',
            'jurusan' => 'TKJ',
            'wali_kelas_id' => $waliKelas->id,
        ]);

        Kelas::create([
            'nama' => 'X IPA 1',
            'tingkat' => '10',
            'jurusan' => 'IPA',
        ]);
    }
}
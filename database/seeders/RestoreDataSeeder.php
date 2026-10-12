<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RestoreDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('PRAGMA foreign_keys=OFF');

        // Urutan: induk dulu, lalu anak
        $tables = ['kelas', 'users', 'jurnal_harian', 'absensi_harian'];

        foreach ($tables as $table) {
            $file = database_path("seeders/backup/{$table}.json");
            if (!file_exists($file)) continue;

            $rows = json_decode(file_get_contents($file), true);
            if (empty($rows)) continue;

            DB::table($table)->delete(); // cegah duplikat

            foreach ($rows as $row) {
                DB::table($table)->insert($row);
            }
        }

        DB::statement('PRAGMA foreign_keys=ON');

        echo "Restore selesai: " . DB::table('users')->count() . " users, "
           . DB::table('kelas')->count() . " kelas, "
           . DB::table('jurnal_harian')->count() . " jurnal, "
           . DB::table('absensi_harian')->count() . " absensi" . PHP_EOL;
    }
}
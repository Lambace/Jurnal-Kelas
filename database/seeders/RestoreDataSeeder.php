<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RestoreDataSeeder extends Seeder
{
    public function run(): void
    {
        $driver = DB::getDriverName();

        // Matikan cek foreign key sesuai jenis database
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF');
        } elseif ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        $tables = ['kelas', 'users', 'jurnal_harian', 'absensi_harian'];

        foreach ($tables as $table) {
            $file = database_path("seeders/backup/{$table}.json");

            if (!file_exists($file)) {
                echo "[{$table}] file tidak ada - dilewati" . PHP_EOL;
                continue;
            }

            $rows = json_decode(file_get_contents($file), true);

            if ($rows === null || empty($rows)) {
                echo "[{$table}] kosong - dilewati" . PHP_EOL;
                continue;
            }

            try {
                DB::table($table)->delete();

                foreach ($rows as $row) {
                    // Tambal password yang hilang dari dump lama
                    if ($table === 'users' && empty($row['password'])) {
                        $row['password'] = Hash::make('password');
                    }
                    DB::table($table)->insert($row);
                }

                echo "[{$table}] OK: " . count($rows) . " baris" . PHP_EOL;
            } catch (\Throwable $e) {
                echo "[{$table}] GAGAL: " . $e->getMessage() . PHP_EOL;
            }
        }

        // Nyalakan kembali cek foreign key
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=ON');
        } elseif ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        echo "RESTORE SELESAI (driver: {$driver})" . PHP_EOL;
    }
}
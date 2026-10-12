<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RestoreDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('PRAGMA foreign_keys=OFF');

        $tables = ['kelas', 'users', 'jurnal_harian', 'absensi_harian'];

        foreach ($tables as $table) {
            $file = database_path("seeders/backup/{$table}.json");

            if (!file_exists($file)) {
                echo "[{$table}] file tidak ada - dilewati" . PHP_EOL;
                continue;
            }

            $rows = json_decode(file_get_contents($file), true);

            if ($rows === null) {
                echo "[{$table}] JSON RUSAK - dilewati" . PHP_EOL;
                continue;
            }

            if (empty($rows)) {
                echo "[{$table}] kosong - dilewati" . PHP_EOL;
                continue;
            }

            try {
                DB::table($table)->delete();

                foreach ($rows as $row) {
                    // Tambal password yang hilang dari dump (akibat $hidden di model)
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

        DB::statement('PRAGMA foreign_keys=ON');
        echo "SELESAI." . PHP_EOL;
    }
}
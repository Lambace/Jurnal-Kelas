<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class RestoreDataSeeder extends Seeder
{
    /** Kolom bertipe DATE (tanggal saja, tanpa jam) */
    private const DATE_COLUMNS = ['tanggal'];

    public function run(): void
    {
        $driver = DB::getDriverName();

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
                    $row = $this->normalizeRow($row);

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

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=ON');
        } elseif ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        echo "RESTORE SELESAI (driver: {$driver})" . PHP_EOL;
    }

    /**
     * Normalisasi nilai dump agar kompatibel MySQL:
     * - Array (kolom JSON)         -> json_encode(...)
     * - ISO-8601 (2026-10-12T12:51:32.000000Z) -> '2026-10-12 12:51:32'
     * - Kolom tanggal              -> '2026-10-12'
     */
    private function normalizeRow(array $row): array
    {
        foreach ($row as $key => $value) {
            if (is_array($value)) {
                $row[$key] = json_encode($value);
            } elseif (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $value)) {
                $carbon = Carbon::parse($value);
                $row[$key] = in_array($key, self::DATE_COLUMNS, true)
                    ? $carbon->format('Y-m-d')
                    : $carbon->format('Y-m-d H:i:s');
            }
        }

        return $row;
    }
}
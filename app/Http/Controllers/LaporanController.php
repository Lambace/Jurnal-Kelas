<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Tampilkan laporan kehadiran dengan filter per kelas dan rentang tanggal.
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function kehadiran(Request $request)
    {
        $user = Auth::user();

        // ⚠️ Pengaman: hanya Admin & Wali Kelas yang boleh akses
        if (!$user->isAdmin() && !$user->isWaliKelas()) {
            abort(403, 'Akses Ditolak');
        }

        // Ambil ID kelas yang diampu (pakai helper pusat)
        $kelasIds = $user->kelasIdsDiampu();

        // Default filter: bulan ini
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Validasi dan normalisasi tanggal
        [$startDate, $endDate] = $this->normalizeDateRange($startDate, $endDate);

        // Hitung total hari efektif (exclude weekend)
        $totalHariEfektif = $this->hitungHariEfektif($startDate, $endDate);

        // ⚠️ Kasus: wali kelas belum ditugaskan ke kelas manapun
        if ($kelasIds->isEmpty()) {
            return view('laporan.kehadiran', [
                'reports'          => collect([]),
                'startDate'        => $startDate,
                'endDate'          => $endDate,
                'kelasNama'        => 'Belum ada kelas ditugaskan',
                'totalHariEfektif' => 0,
                'rekap'            => $this->getEmptyRekap(),
                'kelasIds'         => collect(),
            ]);
        }

        // Query Rekap Kehadiran Per Siswa
        $reports = $this->getRekapKehadiran($kelasIds, $startDate, $endDate);

        // Hitung persentase kehadiran & tidak hadir untuk tiap siswa
        $this->calculatePercentages($reports, $totalHariEfektif);

        // Rekap keseluruhan (untuk ringkasan di atas laporan)
        $rekap = $this->calculateRekap($reports);

        // Nama kelas untuk judul laporan
        $kelasNama = $this->getKelasNama($user);

        return view('laporan.kehadiran', compact(
            'reports',
            'startDate',
            'endDate',
            'kelasNama',
            'totalHariEfektif',
            'rekap',
            'kelasIds'
        ));
    }

    /**
     * Normalisasi rentang tanggal (validasi & swap jika terbalik).
     * 
     * @param string $startDate
     * @param string $endDate
     * @return array [startDate, endDate]
     */
    private function normalizeDateRange(string $startDate, string $endDate): array
    {
        try {
            $startDateObj = Carbon::parse($startDate);
            $endDateObj   = Carbon::parse($endDate);

            // Swap otomatis jika terbalik
            if ($endDateObj->lt($startDateObj)) {
                return [$endDate, $startDate];
            }

            return [$startDate, $endDate];
        } catch (\Exception $e) {
            // Log error untuk debugging
            Log::warning('Invalid date format in LaporanController', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'error' => $e->getMessage()
            ]);

            // Fallback ke bulan ini
            return [
                now()->startOfMonth()->format('Y-m-d'),
                now()->endOfMonth()->format('Y-m-d')
            ];
        }
    }

    /**
     * Ambil rekap kehadiran dari database.
     * 
     * @param \Illuminate\Support\Collection $kelasIds
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Support\Collection
     */
    private function getRekapKehadiran($kelasIds, string $startDate, string $endDate)
    {
        return DB::table('absensi_harian')
            ->join('users', 'absensi_harian.user_id', '=', 'users.id')
            ->join('kelas', 'users.kelas_id', '=', 'kelas.id')
            ->whereIn('absensi_harian.kelas_id', $kelasIds)
            ->whereBetween('absensi_harian.tanggal', [$startDate, $endDate])
            ->select(
                'users.id as user_id',
                'users.nama',
                'users.no_wa_ortu',
                'kelas.nama as nama_kelas',
                'kelas.tingkat',
                DB::raw('SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir'),
                DB::raw('SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as sakit'),
                DB::raw('SUM(CASE WHEN status = "izin" THEN 1 ELSE 0 END) as izin'),
                DB::raw('SUM(CASE WHEN status = "alpha" THEN 1 ELSE 0 END) as alpha'),
                DB::raw('SUM(CASE WHEN status = "terlambat" THEN 1 ELSE 0 END) as terlambat'),
                DB::raw('SUM(CASE WHEN status = "bolos" THEN 1 ELSE 0 END) as bolos'),
                DB::raw('COUNT(*) as total_absensi')
            )
            ->groupBy('users.id', 'users.nama', 'users.no_wa_ortu', 'kelas.nama', 'kelas.tingkat')
            ->orderBy('kelas.nama')
            ->orderBy('users.nama')
            ->get();
    }

    /**
     * Hitung persentase kehadiran untuk setiap siswa.
     * 
     * @param \Illuminate\Support\Collection $reports
     * @param int $totalHariEfektif
     * @return void
     */
    private function calculatePercentages($reports, int $totalHariEfektif): void
    {
        $reports->transform(function ($row) use ($totalHariEfektif) {
            $row->persentase_kehadiran = $totalHariEfektif > 0
                ? round(($row->hadir / $totalHariEfektif) * 100, 1)
                : 0;
            $row->tidak_hadir = $row->sakit + $row->izin + $row->alpha + $row->bolos;
            return $row;
        });
    }

    /**
     * Hitung rekap keseluruhan dari semua siswa.
     * 
     * @param \Illuminate\Support\Collection $reports
     * @return array
     */
    private function calculateRekap($reports): array
    {
        return [
            'total_siswa'     => $reports->count(),
            'total_hadir'     => $reports->sum('hadir'),
            'total_sakit'     => $reports->sum('sakit'),
            'total_izin'      => $reports->sum('izin'),
            'total_alpha'     => $reports->sum('alpha'),
            'total_terlambat' => $reports->sum('terlambat'),
            'total_bolos'     => $reports->sum('bolos'),
        ];
    }

    /**
     * Dapatkan rekap kosong (untuk kasus tidak ada data).
     * 
     * @return array
     */
    private function getEmptyRekap(): array
    {
        return [
            'total_siswa'     => 0,
            'total_hadir'     => 0,
            'total_sakit'     => 0,
            'total_izin'      => 0,
            'total_alpha'     => 0,
            'total_terlambat' => 0,
            'total_bolos'     => 0,
        ];
    }

    /**
     * Dapatkan nama kelas untuk judul laporan.
     * 
     * @param User $user
     * @return string
     */
    private function getKelasNama(User $user): string
    {
        if ($user->isWaliKelas()) {
            return $user->kelasWali()->first()?->nama ?? 'Kelas';
        }
        
        return 'Seluruh Sekolah';
    }

    /**
     * Hitung jumlah hari efektif dalam rentang tanggal (exclude Sabtu & Minggu).
     * 
     * @param string $start
     * @param string $end
     * @return int
     */
    private function hitungHariEfektif(string $start, string $end): int
    {
        $current = Carbon::parse($start);
        $end = Carbon::parse($end);
        $count = 0;

        // Optimasi: batasi maksimal 366 hari (1 tahun + 1 hari kabisat)
        $maxDays = 366;
        $daysProcessed = 0;

        while ($current->lte($end) && $daysProcessed < $maxDays) {
            if (!$current->isWeekend()) {
                $count++;
            }
            $current->addDay();
            $daysProcessed++;
        }

        return $count;
    }
}
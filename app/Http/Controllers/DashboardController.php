<?php

namespace App\Http\Controllers;

use App\Models\JurnalHarian;
use App\Models\AbsensiHarian;
use App\Models\User; // ⚠️ TAMBAHKAN INI agar kode lebih rapi
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
        public function index()
            {
                $user = auth()->user();
                $kelasIds = $user->kelasIdsDiampu();

                // ⚠️ Sesuaikan nama kolom status persetujuan dengan migration Anda
                //    (mis. 'status_persetujuan' atau 'status')
                $totalJurnalMenunggu  = JurnalHarian::whereIn('id_kelas', $kelasIds)->where('status_persetujuan', 'menunggu')->count();
                $totalJurnalDisetujui = JurnalHarian::whereIn('id_kelas', $kelasIds)->where('status_persetujuan', 'disetujui')->count();
                $totalJurnalDitolak   = JurnalHarian::whereIn('id_kelas', $kelasIds)->where('status_persetujuan', 'ditolak')->count();

                $totalSiswa = User::where('peran', 'siswa')->whereIn('kelas_id', $kelasIds)->count();

                // Grafik kehadiran: hanya dari absensi kelas yang diampu
                $grafikKehadiran = AbsensiHarian::whereIn('kelas_id', $kelasIds)
                    ->selectRaw('status, count(*) as total')
                    ->groupBy('status')
                    ->pluck('total', 'status');

                return view('dashboard', compact(
                    'totalJurnalMenunggu',
                    'totalJurnalDisetujui',
                    'totalJurnalDitolak',
                    'totalSiswa',
                    'grafikKehadiran'
                ));
            }
    /**
     * ⚠️ METHOD BARU: API untuk mengambil data absensi berdasarkan rentang tanggal
     * Dipanggil via AJAX dari frontend
     */
    public function getAttendanceData(Request $request)
    {
        $user = Auth::user();
        
        // Validasi input tanggal
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Tentukan kelas yang diizinkan berdasarkan role
        if ($user->isAdmin()) {
            $kelasIds = null; // Semua kelas
        } elseif ($user->isWaliKelas()) {
            $kelasIds = $user->kelasWali()->pluck('id');
            if ($kelasIds->isEmpty()) {
                return response()->json([
                    'labels' => [],
                    'hadir' => [],
                    'tidak_hadir' => [],
                    'total_hadir' => 0,
                    'total_tidak_hadir' => 0
                ]);
            }
        } else {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        $chartData = $this->getDailyAttendanceData($kelasIds, $startDate, $endDate);

        return response()->json($chartData);
    }

    /**
     * Helper: Ambil data absensi per hari berdasarkan rentang tanggal
     */
    private function getDailyAttendanceData($kelasIds, $startDate, $endDate)
    {
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        
        // Batasi maksimal 90 hari untuk performa
        if ($start->diffInDays($end) > 90) {
            $end = $start->copy()->addDays(90);
        }

        $labels = [];
        $dataHadir = [];
        $dataTidakHadir = [];
        $totalHadir = 0;
        $totalTidakHadir = 0;

        $current = $start->copy();
        while ($current->lte($end)) {
            $dateStr = $current->format('Y-m-d');
            $label = $current->format('d M'); // Contoh: "10 Okt"
            $labels[] = $label;

            $query = AbsensiHarian::whereDate('tanggal', $dateStr);
            
            if ($kelasIds !== null) {
                $query->whereIn('kelas_id', $kelasIds);
            }

            $stats = $query->select(
                    DB::raw('SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir'),
                    DB::raw('SUM(CASE WHEN status IN ("sakit", "izin", "alpha", "terlambat", "bolos") THEN 1 ELSE 0 END) as tidak_hadir')
                )
                ->first();

            $hadir = (int) ($stats->hadir ?? 0);
            $tidakHadir = (int) ($stats->tidak_hadir ?? 0);
            
            $dataHadir[] = $hadir;
            $dataTidakHadir[] = $tidakHadir;
            $totalHadir += $hadir;
            $totalTidakHadir += $tidakHadir;

            $current->addDay();
        }

        return [
            'labels' => $labels,
            'hadir' => $dataHadir,
            'tidak_hadir' => $dataTidakHadir,
            'total_hadir' => $totalHadir,
            'total_tidak_hadir' => $totalTidakHadir,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
    }
}
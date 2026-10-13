<?php

namespace App\Http\Controllers;

use App\Models\JurnalHarian;
use App\Models\AbsensiHarian;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $kelasIds = $user->kelasIdsDiampu();

        // Deteksi otomatis nama kolom persetujuan yang ada di tabel
        $col = Schema::hasColumn('jurnal_harian', 'status_persetujuan')
            ? 'status_persetujuan'
            : 'status';

        // ✨ Nama variabel SESUAI yang diminta dashboard.blade.php
        $totalJurnal = JurnalHarian::whereIn('id_kelas', $kelasIds)->count();
        $menunggu    = JurnalHarian::whereIn('id_kelas', $kelasIds)->where($col, 'menunggu')->count();
        $disetujui   = JurnalHarian::whereIn('id_kelas', $kelasIds)->where($col, 'disetujui')->count();
        $ditolak     = JurnalHarian::whereIn('id_kelas', $kelasIds)->where($col, 'ditolak')->count();

        $totalSiswa = User::where('peran', 'siswa')->whereIn('kelas_id', $kelasIds)->count();

        // ✨ Data grafik bulan ini (format array sesuai yang dibaca view)
        $chartData = $this->getDailyAttendanceData(
            $kelasIds,
            now()->startOfMonth()->format('Y-m-d'),
            now()->endOfMonth()->format('Y-m-d')
        );

        // ✨ Daftar jurnal terbaru untuk tabel di dashboard
        $jurnalTerbaru = JurnalHarian::with(['kelas', 'pengisi'])
            ->whereIn('id_kelas', $kelasIds)
            ->latest('tanggal')
            ->limit(10)
            ->get();

        $grafikKehadiran = AbsensiHarian::whereIn('kelas_id', $kelasIds)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('dashboard', compact(
            'totalJurnal',
            'menunggu',
            'disetujui',
            'ditolak',
            'totalSiswa',
            'chartData',
            'jurnalTerbaru',
            'grafikKehadiran'
        ));
    }

    /**
     * API AJAX: data absensi berdasarkan rentang tanggal
     */
    public function getAttendanceData(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $kelasIds = $user->kelasIdsDiampu();

        if (!$user->isAdmin() && $kelasIds->isEmpty()) {
            return response()->json([
                'labels' => [], 'hadir' => [], 'tidak_hadir' => [],
                'total_hadir' => 0, 'total_tidak_hadir' => 0,
            ]);
        }

        return response()->json(
            $this->getDailyAttendanceData(
                $kelasIds,
                $request->input('start_date'),
                $request->input('end_date')
            )
        );
    }

    /**
     * Helper: rekap absensi per hari — SATU query saja (cepat & aman)
     */
    private function getDailyAttendanceData($kelasIds, $startDate, $endDate)
    {
        $start = \Carbon\Carbon::parse($startDate);
        $end   = \Carbon\Carbon::parse($endDate);

        if ($start->diffInDays($end) > 90) {
            $end = $start->copy()->addDays(90);
        }

        $stats = DB::table('absensi_harian')
            ->select(
                DB::raw('DATE(tanggal) as tanggal'),
                DB::raw('SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir'),
                DB::raw('SUM(CASE WHEN status IN ("sakit","izin","alpha","terlambat","bolos") THEN 1 ELSE 0 END) as tidak_hadir')
            )
            ->whereIn('kelas_id', $kelasIds)
            ->whereBetween('tanggal', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get()
            ->keyBy('tanggal');

        $labels = [];
        $dataHadir = [];
        $dataTidakHadir = [];
        $totalHadir = 0;
        $totalTidakHadir = 0;

        $current = $start->copy();
        while ($current->lte($end)) {
            $dateStr = $current->format('Y-m-d');
            $labels[] = $current->format('d M');

            $day = $stats->get($dateStr);
            $hadir = (int) ($day->hadir ?? 0);
            $tidakHadir = (int) ($day->tidak_hadir ?? 0);

            $dataHadir[] = $hadir;
            $dataTidakHadir[] = $tidakHadir;
            $totalHadir += $hadir;
            $totalTidakHadir += $tidakHadir;

            $current->addDay();
        }

        return [
            'labels'            => $labels,
            'hadir'             => $dataHadir,
            'tidak_hadir'       => $dataTidakHadir,
            'total_hadir'       => $totalHadir,
            'total_tidak_hadir' => $totalTidakHadir,
            'start_date'        => $startDate,
            'end_date'          => $endDate,
        ];
    }
}
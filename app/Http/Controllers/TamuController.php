<?php

namespace App\Http\Controllers;

use App\Models\AbsensiHarian;
use App\Models\Kelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TamuController extends Controller
{
    public function index(Request $request)
    {
        // Default 7 hari terakhir
        $startDate = $request->input('start_date', now()->subDays(6)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Batasi maksimal 30 hari untuk performa
        if (now()->parse($startDate)->diffInDays(now()->parse($endDate)) > 30) {
            $startDate = now()->subDays(29)->format('Y-m-d');
        }

        // Ambil semua kelas
        $daftarKelas = Kelas::orderBy('nama')->get();

        // Query data absensi per hari untuk SEMUA kelas
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);

        $labels = [];
        $dataHadir = [];
        $dataTidakHadir = [];
        $totalHadir = 0;
        $totalTidakHadir = 0;

        $current = $start->copy();
        while ($current->lte($end)) {
            $dateStr = $current->format('Y-m-d');
            $labels[] = $current->format('d M');

            $stats = AbsensiHarian::whereDate('tanggal', $dateStr)
                ->select(
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

        $chartData = [
            'labels' => $labels,
            'hadir' => $dataHadir,
            'tidak_hadir' => $dataTidakHadir,
            'total_hadir' => $totalHadir,
            'total_tidak_hadir' => $totalTidakHadir,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];

        return view('tamu', compact('chartData', 'daftarKelas', 'startDate', 'endDate'));
    }

    public function attendanceData(Request $request)
    {
        // Sama seperti index(), tapi return JSON untuk AJAX filter
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);

        if ($start->diffInDays($end) > 30) {
            $end = $start->copy()->addDays(30);
        }

        $labels = [];
        $dataHadir = [];
        $dataTidakHadir = [];
        $totalHadir = 0;
        $totalTidakHadir = 0;

        $current = $start->copy();
        while ($current->lte($end)) {
            $dateStr = $current->format('Y-m-d');
            $labels[] = $current->format('d M');

            $stats = AbsensiHarian::whereDate('tanggal', $dateStr)
                ->select(
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

        return response()->json([
            'labels' => $labels,
            'hadir' => $dataHadir,
            'tidak_hadir' => $dataTidakHadir,
            'total_hadir' => $totalHadir,
            'total_tidak_hadir' => $totalTidakHadir,
        ]);
    }
}
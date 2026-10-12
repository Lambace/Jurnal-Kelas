<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    public function kehadiran(Request $request)
    {
        $user = Auth::user();

        // Pastikan hanya Wali Kelas atau Admin yang bisa akses
        if (!$user->isWaliKelas() && !$user->isAdmin()) {
            abort(403, 'Akses Ditolak');
        }

        // Ambil ID kelas yang diampu (Admin bisa lihat semua, Wali Kelas hanya kelasnya)
        if ($user->isAdmin()) {
            $kelasIds = \App\Models\Kelas::pluck('id');
        } else {
            $kelasIds = $user->kelasWali()->pluck('id');
            
            if ($kelasIds->isEmpty()) {
                return view('laporan.kehadiran', [
                    'reports' => collect([]),
                    'startDate' => now()->startOfMonth()->format('Y-m-d'),
                    'endDate' => now()->endOfMonth()->format('Y-m-d'),
                    'kelasNama' => 'Belum ada kelas'
                ]);
            }
        }

        // Default filter: Bulan ini
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Query Rekap Kehadiran Per Siswa
        $reports = DB::table('absensi_harian')
            ->join('users', 'absensi_harian.user_id', '=', 'users.id')
            ->whereIn('absensi_harian.kelas_id', $kelasIds)
            ->whereBetween('absensi_harian.tanggal', [$startDate, $endDate])
            ->select(
                'users.id as user_id',
                'users.nama',
                DB::raw('SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir'),
                DB::raw('SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as sakit'),
                DB::raw('SUM(CASE WHEN status = "izin" THEN 1 ELSE 0 END) as izin'),
                DB::raw('SUM(CASE WHEN status = "alpha" THEN 1 ELSE 0 END) as alpha'),
                DB::raw('SUM(CASE WHEN status = "terlambat" THEN 1 ELSE 0 END) as terlambat'),
                DB::raw('SUM(CASE WHEN status = "bolos" THEN 1 ELSE 0 END) as bolos')
            )
            ->groupBy('users.id', 'users.nama')
            ->orderBy('users.nama')
            ->get();

        // Ambil nama kelas untuk judul (jika wali kelas)
        $kelasNama = $user->isWaliKelas() 
            ? $user->kelasWali()->first()?->nama ?? 'Kelas' 
            : 'Seluruh Sekolah';

        return view('laporan.kehadiran', compact('reports', 'startDate', 'endDate', 'kelasNama'));
    }
}
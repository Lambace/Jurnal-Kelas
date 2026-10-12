@extends('layouts.app')

@section('title', 'Laporan Kehadiran')
@section('header', 'Laporan Rekapitulasi Kehadiran Siswa')

@section('content')
<div class="space-y-4 md:space-y-6">

    {{-- Filter & Aksi --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6 no-print">
        <form method="GET" action="{{ route('laporan.kehadiran') }}" class="flex flex-col md:flex-row gap-3 items-end">
            <div class="flex-1 w-full">
                <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-cyan-500 focus:ring-1 focus:ring-cyan-200 outline-none text-sm">
            </div>
            <div class="flex-1 w-full">
                <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-cyan-500 focus:ring-1 focus:ring-cyan-200 outline-none text-sm">
            </div>
            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="flex-1 md:flex-none px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-sm font-medium transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
                <button type="button" onclick="window.print()" class="flex-1 md:flex-none px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm font-medium transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak
                </button>
            </div>
        </form>
    </div>

    {{-- Tabel Laporan --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 md:px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-base md:text-lg font-semibold text-gray-800">
                Rekap Kehadiran: <span class="text-cyan-600">{{ $kelasNama }}</span>
            </h3>
            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 md:px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-3 md:px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Siswa</th>
                        <th class="px-2 md:px-3 py-3 text-center text-xs font-medium text-green-600 uppercase bg-green-50">H</th>
                        <th class="px-2 md:px-3 py-3 text-center text-xs font-medium text-yellow-600 uppercase bg-yellow-50">S</th>
                        <th class="px-2 md:px-3 py-3 text-center text-xs font-medium text-blue-600 uppercase bg-blue-50">I</th>
                        <th class="px-2 md:px-3 py-3 text-center text-xs font-medium text-red-600 uppercase bg-red-50">A</th>
                        <th class="px-2 md:px-3 py-3 text-center text-xs font-medium text-orange-600 uppercase bg-orange-50">T</th>
                        <th class="px-2 md:px-3 py-3 text-center text-xs font-medium text-purple-600 uppercase bg-purple-50">B</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($reports as $index => $report)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-3 md:px-4 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                        <td class="px-3 md:px-4 py-3 text-sm font-medium text-gray-800">{{ $report->nama }}</td>
                        <td class="px-2 md:px-3 py-3 text-center text-sm font-bold text-green-600 bg-green-50/50">{{ $report->hadir }}</td>
                        <td class="px-2 md:px-3 py-3 text-center text-sm font-bold text-yellow-600 bg-yellow-50/50">{{ $report->sakit }}</td>
                        <td class="px-2 md:px-3 py-3 text-center text-sm font-bold text-blue-600 bg-blue-50/50">{{ $report->izin }}</td>
                        <td class="px-2 md:px-3 py-3 text-center text-sm font-bold text-red-600 bg-red-50/50">{{ $report->alpha }}</td>
                        <td class="px-2 md:px-3 py-3 text-center text-sm font-bold text-orange-600 bg-orange-50/50">{{ $report->terlambat }}</td>
                        <td class="px-2 md:px-3 py-3 text-center text-sm font-bold text-purple-600 bg-purple-50/50">{{ $report->bolos }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-400 text-sm">
                            Tidak ada data kehadiran pada rentang tanggal ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- CSS Khusus Print: Menyembunyikan sidebar, header, dan tombol saat dicetak --}}
<style>
@media print {
    body * {
        visibility: hidden;
    }
    .bg-white.rounded-xl.shadow-sm.border.border-gray-100.overflow-hidden, 
    .bg-white.rounded-xl.shadow-sm.border.border-gray-100.overflow-hidden * {
        visibility: visible;
    }
    .bg-white.rounded-xl.shadow-sm.border.border-gray-100.overflow-hidden {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none;
        border: none;
    }
    .no-print {
        display: none !important;
    }
}
</style>
@endsection
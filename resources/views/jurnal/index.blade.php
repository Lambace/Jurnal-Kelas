@extends('layouts.app')

@section('title', 'Jurnal Harian')
@section('header', 'Jurnal Harian')

@section('content')
<div class="space-y-4 md:space-y-6">
    
    {{-- 1. PERINGATAN: Jika Wali Kelas belum di-assign ke kelas --}}
    @if(isset($belumDiAssign) && $belumDiAssign)
        <div class="p-4 md:p-5 bg-yellow-50 border-l-4 border-yellow-500 text-yellow-800 rounded-lg shadow-sm flex items-start gap-3">
            <svg class="w-6 h-6 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <p class="font-semibold text-sm md:text-base">Anda belum ditugaskan sebagai Wali Kelas</p>
                <p class="text-xs md:text-sm mt-1 text-yellow-700">
                    Silakan hubungi Administrator untuk ditugaskan ke sebuah kelas melalui menu <strong>Manajemen Kelas</strong>. 
                    Setelah di-assign, jurnal dari kelas tersebut akan otomatis muncul di halaman ini.
                </p>
            </div>
        </div>
    @endif

    {{-- 2. Header & Tombol Aksi --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-lg md:text-xl font-semibold text-gray-800">Daftar Jurnal</h2>
            <p class="text-gray-500 text-sm mt-1">
                @if(auth()->user()->isAdmin())
                    Kelola dan verifikasi jurnal dari seluruh kelas
                @elseif(auth()->user()->isWaliKelas())
                    Pantau dan verifikasi jurnal dari kelas yang Anda ampu
                @else
                    Kelola riwayat pengisian jurnal harian Anda
                @endif
            </p>
        </div>
        
        {{-- Tombol Isi Jurnal Baru (Hanya untuk Guru, Wali Kelas, dan Siswa) --}}
        @if(in_array(auth()->user()->peran, ['guru', 'wali_kelas', 'siswa']))
        <a href="{{ route('jurnal.create') }}" 
           class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white px-5 py-2.5 rounded-xl font-medium shadow-lg shadow-cyan-200 transition-all duration-300 flex items-center gap-2 text-sm transform hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Isi Jurnal Baru
        </a>
        @endif
    </div>

    {{-- 3. Tabel Data Jurnal --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Tanggal</th>
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Kelas</th>
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap hidden md:table-cell">Pengisi</th>
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap hidden lg:table-cell">Kehadiran</th>
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Status</th>
                        <th class="px-3 md:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($jurnals as $jurnal)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-3 md:px-6 py-3 md:py-4 text-sm text-gray-900 whitespace-nowrap">
                            {{ $jurnal->tanggal->format('d M Y') }}
                        </td>
                        <td class="px-3 md:px-6 py-3 md:py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                            {{ $jurnal->kelas->nama ?? '-' }}
                        </td>
                        <td class="px-3 md:px-6 py-3 text-sm text-gray-500 whitespace-nowrap hidden md:table-cell">
                            {{ $jurnal->pengisi->nama ?? '-' }}
                        </td>
                        <td class="px-3 md:px-6 py-3 text-sm whitespace-nowrap hidden lg:table-cell">
                            <span class="text-green-600 font-medium">H:{{ $jurnal->kehadiran['hadir'] ?? 0 }}</span>
                            <span class="text-yellow-600 font-medium ml-2">S:{{ $jurnal->kehadiran['sakit'] ?? 0 }}</span>
                            <span class="text-blue-600 font-medium ml-2">I:{{ $jurnal->kehadiran['izin'] ?? 0 }}</span>
                        </td>
                        <td class="px-3 md:px-6 py-3 whitespace-nowrap">
                            @if($jurnal->status === 'disetujui')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    Disetujui
                                </span>
                            @elseif($jurnal->status === 'ditolak')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                    Ditolak
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                    Menunggu
                                </span>
                            @endif
                        </td>
                        <td class="px-3 md:px-6 py-3 text-right whitespace-nowrap relative z-10">
                            <div class="flex items-center justify-end gap-1.5">
                                {{-- Tombol Lihat (Semua Role) --}}
                                <a href="{{ route('jurnal.show', $jurnal->id) }}" 
                                   class="inline-flex items-center gap-1 text-cyan-600 hover:text-cyan-900 text-xs md:text-sm font-medium px-2.5 py-1.5 rounded-lg hover:bg-cyan-50 transition relative z-20" title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span class="hidden md:inline">Lihat</span>
                                </a>

                                {{-- Tombol Verifikasi (Khusus Admin & Wali Kelas, hanya jika status menunggu) --}}
                                @if($jurnal->status === 'menunggu_verifikasi' && (auth()->user()->isAdmin() || auth()->user()->isWaliKelas()))
                                    <a href="{{ route('jurnal.show', $jurnal->id) }}" 
                                       class="inline-flex items-center gap-1 text-purple-600 hover:text-purple-900 text-xs md:text-sm font-medium px-2.5 py-1.5 rounded-lg hover:bg-purple-50 transition relative z-20" title="Verifikasi Jurnal">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span class="hidden md:inline">Verifikasi</span>
                                    </a>
                                @endif

                                {{-- Tombol Edit & Hapus (Khusus Pemilik Jurnal atau Admin, hanya jika status menunggu) --}}
                                @if($jurnal->status === 'menunggu_verifikasi' && (auth()->id() === $jurnal->id_pengisi || auth()->user()->isAdmin()))
                                    <a href="{{ route('jurnal.edit', $jurnal->id) }}" 
                                       class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-900 text-xs md:text-sm font-medium px-2.5 py-1.5 rounded-lg hover:bg-indigo-50 transition relative z-20" title="Edit Jurnal">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span class="hidden md:inline">Edit</span>
                                    </a>
                                    <form action="{{ route('jurnal.destroy', $jurnal->id) }}" method="POST" class="inline relative z-20" onsubmit="return confirm('Yakin ingin menghapus jurnal ini? Tindakan ini tidak dapat dibatalkan.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 text-red-600 hover:text-red-900 text-xs md:text-sm font-medium px-2.5 py-1.5 rounded-lg hover:bg-red-50 transition" title="Hapus Jurnal">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            <span class="hidden md:inline">Hapus</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 md:px-6 py-10 md:py-16 text-center text-gray-400 text-sm md:text-base">
                            <svg class="w-14 h-14 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            @if(isset($belumDiAssign) && $belumDiAssign)
                                <p class="font-medium text-gray-500">Belum ada jurnal yang ditampilkan.</p>
                                <p class="text-xs mt-1">Pastikan Anda sudah di-assign ke kelas oleh Administrator.</p>
                            @else
                                <p class="font-medium text-gray-500">Belum ada data jurnal.</p>
                                @if(in_array(auth()->user()->peran, ['guru', 'wali_kelas', 'siswa']))
                                    <p class="text-xs mt-1">Klik tombol "Isi Jurnal Baru" untuk memulai.</p>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($jurnals->hasPages())
            <div class="px-4 md:px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $jurnals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
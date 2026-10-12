@extends('layouts.app')

@section('title', 'Detail Jurnal')
@section('header', 'Detail Jurnal Harian')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    
    <!-- Header Info -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $jurnal->kelas->nama ?? '-' }}</h2>
                <p class="text-gray-600 mt-1">{{ $jurnal->tanggal->format('l, d F Y') }}</p>
                <p class="text-sm text-gray-500 mt-1">Diisi oleh: <strong>{{ $jurnal->pengisi->nama ?? '-' }}</strong></p>
            </div>
            <div>
                @if($jurnal->status === 'disetujui')
                    <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-semibold">✓ Disetujui</span>
                @elseif($jurnal->status === 'ditolak')
                    <span class="px-4 py-2 bg-red-100 text-red-800 rounded-full text-sm font-semibold">✗ Ditolak</span>
                @else
                    <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">⌛ Menunggu</span>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- 1. Rekap Kehadiran (LENGKAP 6 STATUS) -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Kehadiran
            </h3>
            
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <!-- Hadir -->
                <div class="bg-green-50 rounded-lg p-4 text-center border border-green-200">
                    <div class="text-3xl font-bold text-green-600">{{ $jurnal->kehadiran['hadir'] ?? 0 }}</div>
                    <div class="text-xs text-green-700 font-medium mt-1">HADIR</div>
                </div>

                <!-- Sakit -->
                <div class="bg-yellow-50 rounded-lg p-4 text-center border border-yellow-200">
                    <div class="text-3xl font-bold text-yellow-600">{{ $jurnal->kehadiran['sakit'] ?? 0 }}</div>
                    <div class="text-xs text-yellow-700 font-medium mt-1">SAKIT</div>
                </div>

                <!-- Izin -->
                <div class="bg-blue-50 rounded-lg p-4 text-center border border-blue-200">
                    <div class="text-3xl font-bold text-blue-600">{{ $jurnal->kehadiran['izin'] ?? 0 }}</div>
                    <div class="text-xs text-blue-700 font-medium mt-1">IZIN</div>
                </div>

                <!-- Alpha -->
                <div class="bg-red-50 rounded-lg p-4 text-center border border-red-200">
                    <div class="text-3xl font-bold text-red-600">{{ $jurnal->kehadiran['alpha'] ?? 0 }}</div>
                    <div class="text-xs text-red-700 font-medium mt-1">ALPHA</div>
                </div>

                <!-- Terlambat -->
                <div class="bg-orange-50 rounded-lg p-4 text-center border border-orange-200">
                    <div class="text-3xl font-bold text-orange-600">{{ $jurnal->terlambat ?? 0 }}</div>
                    <div class="text-xs text-orange-700 font-medium mt-1">TERLAMBAT</div>
                </div>

                <!-- Bolos -->
                @php
                    $bolosCount = $jurnal->absensi->where('status', 'bolos')->count();
                @endphp
                <div class="bg-purple-50 rounded-lg p-4 text-center border border-purple-200">
                    <div class="text-3xl font-bold text-purple-600">{{ $bolosCount }}</div>
                    <div class="text-xs text-purple-700 font-medium mt-1">BOLOS</div>
                </div>
            </div>

            <!-- Total Siswa -->
            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600">Total Siswa:</span>
                    <span class="font-bold text-gray-800">{{ ($jurnal->kehadiran['hadir'] ?? 0) + ($jurnal->kehadiran['sakit'] ?? 0) + ($jurnal->kehadiran['izin'] ?? 0) + ($jurnal->kehadiran['alpha'] ?? 0) + ($jurnal->terlambat ?? 0) + $bolosCount }}</span>
                </div>
            </div>
        </div>

        <!-- 2. Detail Absensi Per Siswa -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                Detail Absensi Siswa
            </h3>
            
            <div class="space-y-2 max-h-96 overflow-y-auto pr-2">
                @forelse($jurnal->absensi->sortBy('siswa.nama') as $absensi)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-sm">
                                {{ $loop->iteration }}
                            </div>
                            <div>
                                <div class="font-medium text-gray-800">{{ $absensi->siswa->nama ?? 'N/A' }}</div>
                                @if($absensi->keterangan)
                                    <div class="text-xs text-gray-500">{{ $absensi->keterangan }}</div>
                                @endif
                            </div>
                        </div>
                        @if($absensi->status === 'hadir')
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">HADIR</span>
                        @elseif($absensi->status === 'sakit')
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">SAKIT</span>
                        @elseif($absensi->status === 'izin')
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">IZIN</span>
                        @elseif($absensi->status === 'alpha')
                            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">ALPHA</span>
                        @elseif($absensi->status === 'terlambat')
                            <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-semibold">TERLAMBAT</span>
                        @elseif($absensi->status === 'bolos')
                            <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-semibold">BOLOS</span>
                        @endif
                    </div>
                @empty
                    <p class="text-center text-gray-500 text-sm py-8">Tidak ada data absensi.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- 3. Catatan & Informasi Lainnya -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Tambahan</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-medium text-gray-700 mb-2">Kondisi Kelas</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between py-1 border-b border-gray-100">
                        <span class="text-gray-600">Kebersihan:</span>
                        <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $jurnal->kebersihan)) }}</span>
                    </div>
                    @if($jurnal->suhu_ruangan)
                    <div class="flex justify-between py-1 border-b border-gray-100">
                        <span class="text-gray-600">Suhu Ruangan:</span>
                        <span class="font-medium">{{ $jurnal->suhu_ruangan }}</span>
                    </div>
                    @endif
                    @if($jurnal->catatan_fasilitas)
                    <div class="flex justify-between py-1 border-b border-gray-100">
                        <span class="text-gray-600">Catatan Fasilitas:</span>
                        <span class="font-medium">{{ $jurnal->catatan_fasilitas }}</span>
                    </div>
                    @endif
                </div>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-700 mb-2">Pembelajaran</h4>
                <div class="space-y-2 text-sm">
                    @if($jurnal->mata_pelajaran)
                    <div class="flex justify-between py-1 border-b border-gray-100">
                        <span class="text-gray-600">Mata Pelajaran:</span>
                        <span class="font-medium">{{ $jurnal->mata_pelajaran }}</span>
                    </div>
                    @endif
                    @if($jurnal->topik_materi)
                    <div class="flex justify-between py-1 border-b border-gray-100">
                        <span class="text-gray-600">Topik Materi:</span>
                        <span class="font-medium">{{ $jurnal->topik_materi }}</span>
                    </div>
                    @endif
                    @if($jurnal->metode_pembelajaran)
                    <div class="flex justify-between py-1 border-b border-gray-100">
                        <span class="text-gray-600">Metode:</span>
                        <span class="font-medium">{{ $jurnal->metode_pembelajaran }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($jurnal->catatan_pribadi_guru || $jurnal->kejadian_positif || $jurnal->pelanggaran || $jurnal->siswa_bermasalah)
        <div class="mt-6 pt-6 border-t border-gray-200">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Catatan</h4>
            @if($jurnal->catatan_pribadi_guru)
            <div class="mb-3">
                <span class="text-xs font-medium text-gray-600">Catatan Pribadi:</span>
                <p class="text-sm text-gray-700 mt-1">{{ $jurnal->catatan_pribadi_guru }}</p>
            </div>
            @endif
            @if($jurnal->kejadian_positif)
            <div class="mb-3">
                <span class="text-xs font-medium text-gray-600">Kejadian Positif:</span>
                <p class="text-sm text-gray-700 mt-1">{{ $jurnal->kejadian_positif }}</p>
            </div>
            @endif
            @if($jurnal->pelanggaran)
            <div class="mb-3">
                <span class="text-xs font-medium text-gray-600">Pelanggaran:</span>
                <p class="text-sm text-gray-700 mt-1">{{ $jurnal->pelanggaran }}</p>
            </div>
            @endif
            @if($jurnal->siswa_bermasalah)
            <div class="mb-3">
                <span class="text-xs font-medium text-gray-600">Siswa Bermasalah:</span>
                <p class="text-sm text-gray-700 mt-1">{{ $jurnal->siswa_bermasalah }}</p>
            </div>
            @endif
        </div>
        @endif
    </div>

    <!-- Tombol Aksi -->
    <div class="flex flex-col sm:flex-row justify-between gap-3">
        <a href="{{ route('jurnal.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition text-center">
            ← Kembali
        </a>
        <div class="flex gap-3">
            @if($jurnal->status === 'menunggu_verifikasi' && (auth()->user()->isAdmin() || auth()->user()->isWaliKelas()))
                <a href="{{ route('jurnal.edit', $jurnal->id) }}" class="px-6 py-3 bg-yellow-500 text-white rounded-lg font-medium hover:bg-yellow-600 transition">
                    Edit Jurnal
                </a>
                <button onclick="showVerifikasiModal()" class="px-6 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition">
                    Verifikasi
                </button>
            @endif
        </div>
    </div>
</div>

<!-- Modal Verifikasi (jika diperlukan) -->
@if($jurnal->status === 'menunggu_verifikasi' && (auth()->user()->isAdmin() || auth()->user()->isWaliKelas()))
<div id="verifikasiModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold mb-4">Verifikasi Jurnal</h3>
        <form action="{{ route('jurnal.verifikasi', $jurnal->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" id="verifikasiStatus" onchange="toggleCatatanPenolakan()" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                    <option value="disetujui">Disetujui</option>
                    <option value="ditolak">Ditolak</option>
                </select>
            </div>
            <div id="catatanPenolakanField" class="hidden mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Penolakan</label>
                <textarea name="catatan_penolakan" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2"></textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="hideVerifikasiModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function showVerifikasiModal() {
    document.getElementById('verifikasiModal').classList.remove('hidden');
}
function hideVerifikasiModal() {
    document.getElementById('verifikasiModal').classList.add('hidden');
}
function toggleCatatanPenolakan() {
    const status = document.getElementById('verifikasiStatus').value;
    const field = document.getElementById('catatanPenolakanField');
    if (status === 'ditolak') {
        field.classList.remove('hidden');
    } else {
        field.classList.add('hidden');
    }
}
</script>
@endif
@endsection
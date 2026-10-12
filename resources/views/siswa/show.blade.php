@extends('layouts.app')

@section('title', 'Detail Siswa')
@section('header', 'Detail Siswa')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-white text-2xl font-bold">
                {{ strtoupper(substr($siswa->nama, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $siswa->nama }}</h2>
                <p class="text-gray-500">{{ $siswa->email }}</p>
                <span class="inline-block mt-1 px-3 py-1 text-xs font-semibold rounded-full bg-cyan-100 text-cyan-800">{{ $siswa->kelas->nama ?? 'Belum ada kelas' }}</span>
            </div>
        </div>

        <div class="border-t pt-4">
            <h3 class="font-semibold text-gray-700 mb-3">‍👩‍👦 Informasi Orang Tua / Wali</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Nama Orang Tua</p>
                    <p class="font-medium text-gray-800">{{ $siswa->nama_ortu ?: '-' }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Nomor WhatsApp</p>
                    @if($siswa->no_wa_ortu)
                        <p class="font-medium text-green-600">+62 {{ $siswa->no_wa_ortu }}</p>
                        <a href="https://wa.me/62{{ ltrim($siswa->no_wa_ortu, '0') }}" target="_blank"
                           class="inline-block mt-2 text-xs bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 transition">
                            💬 Kirim Pesan WA
                        </a>
                    @else
                        <p class="text-gray-400">-</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-between">
        <a href="{{ route('siswa.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center gap-1 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
        <a href="{{ route('siswa.edit', $siswa) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 transition">✏️ Edit Siswa</a>
    </div>
</div>
@endsection
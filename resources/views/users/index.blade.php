@extends('layouts.app')

@section('title', 'Manajemen User')
@section('header', 'Manajemen User')

@section('content')

    {{-- ️ SAFETY CHECK: Pastikan variabel ada (fallback jika controller gagal kirim) --}}
    @php
        $daftarRole = $daftarRole ?? [
            'admin' => 'Administrator',
            'guru' => 'Guru',
            'wali_kelas' => 'Wali Kelas',
            'tamu' => 'Tamu'
        ];
        
        $daftarKelas = $daftarKelas ?? collect([]);
        $search = $search ?? '';
        $role = $role ?? '';
        $kelasId = $kelasId ?? '';
    @endphp

<div class="space-y-4 md:space-y-6">

    {{-- 🌟 INFO BOX: Penjelasan Pemisahan Data --}}
    <div class="p-4 bg-blue-50 border-l-4 border-blue-500 rounded-lg shadow-sm flex items-start gap-3">
        <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        
    </div>

    {{-- Header dengan Jarak yang Jelas --}}
    <div class="flex flex-col gap-4 mb-2">
        <div>
            <h2 class="text-lg md:text-xl font-semibold text-gray-800">Daftar Pengguna Sistem</h2>
            <p class="text-gray-500 text-sm mt-1">Kelola akses, peran, dan kredensial staff.</p>
        </div>
        
        <div class="flex justify-start md:justify-end">
            <a href="{{ route('users.create') }}" 
               class="w-full md:w-auto bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2.5 rounded-lg flex items-center justify-center gap-2 text-sm font-medium transition shadow-sm">
                
                Buat User Baru
            </a>
        </div>
    </div>

    {{-- Filter & Search --}}
    <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div class="md:col-span-2">
            <input type="text" name="search" value="{{ $search ?? '' }}" 
                placeholder="Cari nama atau email..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-cyan-500 focus:ring-1 focus:ring-cyan-200 outline-none text-sm">
        </div>
        <div>
            <select name="role" onchange="this.form.submit()"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-cyan-500 focus:ring-1 focus:ring-cyan-200 outline-none text-sm bg-white">
                <option value="">Semua Peran</option>
                @foreach($daftarRole as $value => $label)
                    <option value="{{ $value }}" {{ ($role ?? '') == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <select name="kelas_id" onchange="this.form.submit()"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-cyan-500 focus:ring-1 focus:ring-cyan-200 outline-none text-sm bg-white">
                <option value="">Semua Kelas</option>
                @foreach($daftarKelas as $k)
                    <option value="{{ $k->id }}" {{ ($kelasId ?? '') == $k->id ? 'selected' : '' }}>
                        {{ $k->nama }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- Tabel User --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Nama</th>
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap hidden md:table-cell">Email</th>
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Peran</th>
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap hidden lg:table-cell">Kelas</th>
                        <th class="px-3 md:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-3 md:px-6 py-3 md:py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                    {{ strtoupper(substr($user->nama, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 text-sm md:text-base">{{ $user->nama }}</div>
                                    <div class="text-xs text-gray-500 md:hidden">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 md:px-6 py-3 md:py-4 text-sm text-gray-600 whitespace-nowrap hidden md:table-cell">{{ $user->email }}</td>
                        <td class="px-3 md:px-6 py-3 md:py-4 whitespace-nowrap">
                            @if($user->peran === 'admin')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700">👑 Admin</span>
                            @elseif($user->peran === 'guru')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">👨‍🏫 Guru</span>
                            @elseif($user->peran === 'wali_kelas')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">👨‍💼 Wali Kelas</span>
                            @elseif($user->peran === 'tamu')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">👤 Tamu</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-cyan-100 text-cyan-700">{{ ucfirst($user->peran) }}</span>
                            @endif
                        </td>
                        <td class="px-3 md:px-6 py-3 md:py-4 text-sm text-gray-600 whitespace-nowrap hidden lg:table-cell">
                            {{ $user->kelas->nama ?? '-' }}
                        </td>
                        <td class="px-3 md:px-6 py-3 md:py-4 text-right whitespace-nowrap">
                            <a href="{{ route('users.edit', $user->id) }}" 
                                class="inline-flex items-center gap-1 text-cyan-600 hover:text-cyan-900 text-xs md:text-sm font-medium px-3 py-1.5 rounded-lg hover:bg-cyan-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <span class="hidden sm:inline">Edit</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 md:px-6 py-8 md:py-12 text-center text-gray-400 text-sm">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Tidak ada user yang ditemukan dengan filter tersebut.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-4 md:px-6 py-3 border-t border-gray-200 bg-gray-50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
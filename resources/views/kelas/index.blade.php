@extends('layouts.app')
@section('title', 'Manajemen Kelas')
@section('header', 'Manajemen Kelas')

@section('content')
<div class="space-y-4 md:space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <p class="text-gray-500 text-sm">Kelola rombongan belajar dan wali kelas</p>
        <a href="{{ route('kelas.create') }}" class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-medium shadow-lg shadow-cyan-200 transition-all duration-300 flex items-center gap-2 text-sm w-full sm:w-auto justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Kelas Baru
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Nama Kelas</th>
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap hidden md:table-cell">Wali Kelas</th>
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap hidden lg:table-cell">Jumlah Siswa</th>
                        <th class="px-3 md:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($kelas as $k)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-3 md:px-6 py-3 md:py-4">
                            <div class="font-bold text-gray-900 text-sm md:text-base">{{ $k->nama }}</div>
                            <div class="text-xs text-gray-500">Tingkat {{ $k->tingkat }} - {{ $k->jurusan }}</div>
                            <div class="md:hidden text-xs text-gray-500 mt-1">Wali: {{ $k->waliKelas->nama ?? 'Belum diassign' }}</div>
                        </td>
                        <td class="px-3 md:px-6 py-3 text-sm text-gray-600 whitespace-nowrap hidden md:table-cell">{{ $k->waliKelas->nama ?? '<span class="text-gray-400">Belum diassign</span>' }}</td>
                        <td class="px-3 md:px-6 py-3 text-sm text-gray-600 whitespace-nowrap hidden lg:table-cell">{{ $k->users()->count() }} Siswa</td>
                        <td class="px-3 md:px-6 py-3 text-right whitespace-nowrap relative z-10">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('kelas.edit', $k->id) }}" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-900 text-xs md:text-sm font-medium px-3 py-1.5 rounded-lg hover:bg-indigo-50 transition relative z-20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </a>
                                <form action="{{ route('kelas.destroy', $k->id) }}" method="POST" class="inline relative z-20" onsubmit="return confirm('Yakin hapus kelas ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 text-red-600 hover:text-red-900 text-xs md:text-sm font-medium px-3 py-1.5 rounded-lg hover:bg-red-50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 md:px-6 py-8 md:py-12 text-center text-gray-400 text-sm md:text-base">Belum ada data kelas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($kelas->hasPages())<div class="px-4 md:px-6 py-3 border-t border-gray-200">{{ $kelas->links() }}</div>@endif
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Data Siswa')
@section('header', 'Data Siswa')

@section('content')
<div class="space-y-4 md:space-y-6">
    
    {{-- Helper untuk Generate Sort Link --}}
            @php
            // 🛡️ Default agar tidak pernah "undefined variable"
            $sort = $sort ?? 'nama';
            $direction = $direction ?? 'asc';

            // 🛡️ Anti redeclare: hanya definisikan fungsi jika belum ada
            if (!function_exists('sortLink')) {
                function sortLink($column, $label, $currentSort, $currentDirection)
                {
                    $nextDirection = ($currentSort === $column && $currentDirection === 'asc') ? 'desc' : 'asc';

                    $params = array_merge(request()->query(), [
                        'sort' => $column,
                        'direction' => $nextDirection,
                        'page' => null,
                    ]);

                    $url = request()->url() . '?' . http_build_query(array_filter($params));

                    if ($currentSort === $column) {
                        $icon = $currentDirection === 'asc'
                            ? '<svg class="w-3 h-3 ml-1 text-cyan-600" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z"/></svg>'
                            : '<svg class="w-3 h-3 ml-1 text-cyan-600" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 12.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l2.293-2.293a1 1 0 011.414 0z"/></svg>';
                    } else {
                        $icon = '<svg class="w-3 h-3 ml-1 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M5 12a1 1 0 102 0V6.414l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L5 6.414V12zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z"/></svg>';
                    }

                    $textColor = $currentSort === $column ? 'text-cyan-700' : 'text-gray-500 hover:text-cyan-600';

                    return '<a href="' . $url . '" class="inline-flex items-center ' . $textColor . ' transition font-medium uppercase">'
                        . $label . $icon . '</a>';
                }
            }
        @endphp

    {{-- Header & Tombol Aksi --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <p class="text-gray-500 text-sm">Kelola data siswa dan kontak orang tua. Klik header tabel untuk mengurutkan.</p>
        <div class="flex flex-wrap gap-2 w-full sm:w-auto">
            
            @if(auth()->user()->peran === 'admin')
            <form action="{{ route('siswa.deleteAll') }}" method="POST" class="flex-1 sm:flex-none" 
                  onsubmit="return confirm('⚠️ PERINGATAN KERAS:\n\nAnda akan menghapus SEMUA data siswa secara PERMANEN!\nTindakan ini TIDAK DAPAT dibatalkan.\n\nLanjutkan?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-xl font-medium shadow-lg shadow-red-200 transition-all duration-300 flex items-center justify-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus Semua
                </button>
            </form>
            @endif

            <a href="{{ route('siswa.export') }}" 
               class="flex-1 sm:flex-none bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-xl font-medium shadow-lg shadow-green-200 transition-all duration-300 flex items-center justify-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export Excel
            </a>
            
            <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')" 
                    class="flex-1 sm:flex-none bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-medium shadow-lg shadow-indigo-200 transition-all duration-300 flex items-center justify-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Import Excel
            </button>

            <a href="{{ route('siswa.create') }}" 
               class="flex-1 sm:flex-none bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white px-4 py-2.5 rounded-xl font-medium shadow-lg shadow-cyan-200 transition-all duration-300 flex items-center justify-center gap-2 text-sm transform hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Siswa Baru
            </a>
        </div>
    </div>

    {{-- Error Import --}}
    @if($errors->has('import'))
        <div class="p-3 md:p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-lg shadow-sm">
            <p class="font-semibold mb-1 text-xs md:text-sm">Gagal Import Data:</p>
            <ul class="list-disc list-inside space-y-1 text-xs md:text-sm">
                @foreach($errors->get('import') as $msg)
                    <li>{{ $msg }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- MODAL IMPORT --}}
    <div id="importModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="document.getElementById('importModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Import Data Siswa</h3>
                                <div class="mt-4">
                                    <p class="text-sm text-gray-500 mb-3">
                                        Upload file Excel (.xlsx, .xls) atau CSV. <br>
                                        <a href="{{ route('siswa.export') }}" class="text-indigo-600 hover:underline font-medium">Download Template Format</a> terlebih dahulu untuk memastikan kolom sesuai.
                                    </p>
                                    <input type="file" name="file_import" accept=".xlsx,.xls,.csv" required
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">
                            Upload & Import
                        </button>
                        <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="mt-3 sm:mt-0 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Tabel Data Siswa dengan Sort --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        {{-- Kolom Nama Siswa (bisa di-sort) --}}
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium whitespace-nowrap">
                            {!! sortLink('nama', 'Nama Siswa', $sort, $direction) !!}
                        </th>
                        
                        {{-- Kolom Kelas (bisa di-sort) --}}
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium whitespace-nowrap hidden md:table-cell">
                            {!! sortLink('nama_kelas', 'Kelas', $sort, $direction) !!}
                        </th>
                        
                        {{-- Kolom Nama Orang Tua (bisa di-sort) --}}
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium whitespace-nowrap hidden lg:table-cell">
                            {!! sortLink('nama_ortu', 'Nama Orang Tua', $sort, $direction) !!}
                        </th>
                        
                        {{-- Kolom No. WhatsApp (bisa di-sort) --}}
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium whitespace-nowrap hidden lg:table-cell">
                            {!! sortLink('no_wa_ortu', 'No. WhatsApp', $sort, $direction) !!}
                        </th>
                        
                        {{-- Kolom Aksi (TIDAK bisa di-sort) --}}
                        <th class="px-3 md:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($siswa as $s)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-3 md:px-6 py-3 md:py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-cyan-100 text-cyan-700 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                    {{ strtoupper(substr($s->nama, 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-medium text-gray-900 text-sm md:text-base truncate">{{ $s->nama }}</div>
                                    <div class="text-xs text-gray-500 truncate">{{ $s->email }}</div>
                                    <div class="md:hidden text-xs text-gray-500 mt-1">
                                        Kelas: {{ $s->kelas->nama ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 md:px-6 py-3 text-sm text-gray-700 whitespace-nowrap hidden md:table-cell">
                            {{ $s->kelas->nama ?? '-' }}
                        </td>
                        <td class="px-3 md:px-6 py-3 text-sm text-gray-700 whitespace-nowrap hidden lg:table-cell">
                            {{ $s->nama_ortu ?: '-' }}
                        </td>
                        <td class="px-3 md:px-6 py-3 text-sm whitespace-nowrap hidden lg:table-cell">
                            @if($s->no_wa_ortu)
                                <span class="text-green-600 font-medium">{{ $s->no_wa_ortu }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-3 md:px-6 py-3 text-right whitespace-nowrap relative z-10">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('siswa.show', $s) }}" 
                                   class="inline-flex items-center gap-1 text-cyan-600 hover:text-cyan-900 text-xs md:text-sm font-medium px-2 md:px-3 py-1.5 rounded-lg hover:bg-cyan-50 transition relative z-20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span class="hidden md:inline">Detail</span>
                                </a>
                                <a href="{{ route('siswa.edit', $s) }}" 
                                   class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-900 text-xs md:text-sm font-medium px-2 md:px-3 py-1.5 rounded-lg hover:bg-indigo-50 transition relative z-20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    <span class="hidden md:inline">Edit</span>
                                </a>
                                <form action="{{ route('siswa.destroy', $s) }}" method="POST" class="inline relative z-20" onsubmit="return confirm('Yakin hapus siswa ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 text-red-600 hover:text-red-900 text-xs md:text-sm font-medium px-2 md:px-3 py-1.5 rounded-lg hover:bg-red-50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        <span class="hidden md:inline">Hapus</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 md:px-6 py-8 md:py-12 text-center text-gray-400 text-sm md:text-base">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Belum ada data siswa. Klik "Tambah Siswa Baru" untuk memulai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($siswa->hasPages())
            <div class="px-4 md:px-6 py-3 border-t border-gray-200">{{ $siswa->links() }}</div>
        @endif
    </div>
</div>
@endsection
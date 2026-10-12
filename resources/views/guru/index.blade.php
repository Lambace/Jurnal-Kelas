@extends('layouts.app')

@section('title', 'Data Guru')
@section('header', 'Manajemen Data Guru')

@section('content')
<div class="space-y-4 md:space-y-6">
    
    {{-- Header & Tombol Aksi --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <p class="text-gray-500 text-sm">Kelola data pengajar di sekolah</p>
        <div class="flex flex-wrap gap-2 w-full sm:w-auto">
            <!-- Tombol Export Excel -->
            <a href="{{ route('guru.export') }}" 
               class="flex-1 sm:flex-none bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-xl font-medium shadow-lg shadow-green-200 transition-all duration-300 flex items-center justify-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export Excel
            </a>
            
            <!-- Tombol Import Excel (Buka Modal) -->
            <button onclick="document.getElementById('importModal').classList.remove('hidden')" 
                    class="flex-1 sm:flex-none bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-medium shadow-lg shadow-indigo-200 transition-all duration-300 flex items-center justify-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Import Excel
            </button>

            <!-- Tombol Tambah Guru -->
            <a href="{{ route('guru.create') }}" 
               class="flex-1 sm:flex-none bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white px-4 py-2.5 rounded-xl font-medium shadow-lg shadow-cyan-200 transition-all duration-300 flex items-center justify-center gap-2 text-sm transform hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Guru Baru
            </a>
        </div>
    </div>

    {{-- Error Import --}}
    @if($errors->has('import'))
        <div class="p-3 md:p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-lg shadow-sm">
            <p class="font-semibold mb-1 text-xs md:text-sm">Gagal Import Data:</p>
            <ul class="list-disc list-inside space-y-1 text-xs md:text-sm">
                @foreach($errors->get('import') as $errorMessages)
                    @foreach($errorMessages as $msg)
                        <li>{{ $msg }}</li>
                    @endforeach
                @endforeach
            </ul>
        </div>
    @endif

    {{-- MODAL IMPORT --}}
    <div id="importModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('importModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('guru.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Import Data Guru</h3>
                                <div class="mt-4">
                                    <p class="text-sm text-gray-500 mb-3">
                                        Upload file Excel (.xlsx, .xls) atau CSV. <br>
                                        <a href="{{ route('guru.export') }}" class="text-indigo-600 hover:underline font-medium">Download Template Format</a> terlebih dahulu untuk memastikan kolom sesuai.
                                    </p>
                                    <input type="file" name="file_import" accept=".xlsx,.xls,.csv" required
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Upload & Import
                        </button>
                        <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Tabel Data Guru --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Nama Guru</th>
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap hidden md:table-cell">NIP</th>
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap hidden lg:table-cell">No. HP</th>
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap hidden md:table-cell">Email</th>
                        <th class="px-3 md:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($guru as $g)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-3 md:px-6 py-3 md:py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-cyan-100 text-cyan-700 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                    {{ strtoupper(substr($g->nama, 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-medium text-gray-900 text-sm md:text-base truncate">{{ $g->nama }}</div>
                                    <div class="md:hidden text-xs text-gray-500 mt-0.5 truncate">{{ $g->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 md:px-6 py-3 text-sm text-gray-600 whitespace-nowrap hidden md:table-cell">{{ $g->nip ?: '-' }}</td>
                        <td class="px-3 md:px-6 py-3 text-sm text-gray-600 whitespace-nowrap hidden lg:table-cell">{{ $g->no_hp ?: '-' }}</td>
                        <td class="px-3 md:px-6 py-3 text-sm text-gray-600 truncate hidden md:table-cell">{{ $g->email }}</td>
                        <td class="px-3 md:px-6 py-3 text-right whitespace-nowrap relative z-10">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('guru.edit', $g->id) }}" 
                                   class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-900 text-xs md:text-sm font-medium px-2 md:px-3 py-1.5 rounded-lg hover:bg-indigo-50 transition relative z-20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    <span class="hidden md:inline">Edit</span>
                                </a>
                                <form action="{{ route('guru.destroy', $g->id) }}" method="POST" class="inline relative z-20" onsubmit="return confirm('Yakin hapus guru ini?')">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Belum ada data guru. Klik "Tambah Guru Baru" untuk memulai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($guru->hasPages())
            <div class="px-4 md:px-6 py-3 border-t border-gray-200">{{ $guru->links() }}</div>
        @endif
    </div>
</div>
@endsection
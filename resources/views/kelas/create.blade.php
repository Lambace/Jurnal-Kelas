@extends('layouts.app')

@section('title', 'Tambah Kelas')
@section('header', 'Tambah Kelas Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-4 md:space-y-6">
    
    @if($errors->any())
        <div class="p-3 md:p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-lg shadow-sm">
            <p class="font-semibold mb-1 text-xs md:text-sm">Terdapat kesalahan pengisian:</p>
            <ul class="list-disc list-inside space-y-1 text-xs md:text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('kelas.store') }}" method="POST" class="space-y-4 md:space-y-6">
        @csrf

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
            <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-3 md:mb-4 pb-2 border-b flex items-center gap-2">
                🏫 Informasi Kelas
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kelas <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Contoh: XII RPL 1"
                        class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition outline-none text-sm md:text-base">
                    <p class="text-xs text-gray-500 mt-1">Format: Tingkat + Jurusan + Nomor (misal: XII RPL 1)</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tingkat <span class="text-red-500">*</span></label>
                    <select name="tingkat" required class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition outline-none text-sm md:text-base bg-white">
                        <option value="10" {{ old('tingkat') == '10' ? 'selected' : '' }}>Kelas 10</option>
                        <option value="11" {{ old('tingkat') == '11' ? 'selected' : '' }}>Kelas 11</option>
                        <option value="12" {{ old('tingkat') == '12' ? 'selected' : '' }}>Kelas 12</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jurusan <span class="text-red-500">*</span></label>
                    <input type="text" name="jurusan" value="{{ old('jurusan') }}" required placeholder="Contoh: RPL, TKJ, IPA"
                        class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition outline-none text-sm md:text-base">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Wali Kelas (Opsional)</label>
                    <select name="wali_kelas_id" class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition outline-none text-sm md:text-base bg-white">
                        <option value="">-- Belum ada wali kelas --</option>
                        @foreach($daftarGuru as $guru)
                            <option value="{{ $guru->id }}" {{ old('wali_kelas_id') == $guru->id ? 'selected' : '' }}>
                                {{ $guru->nama }} {{ $guru->nip ? '(' . $guru->nip . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Dapat diassign atau diubah nanti melalui menu edit kelas</p>
                </div>

            </div>
        </div>

        {{-- Tombol Aksi (Mobile Friendly) --}}
        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-2 pb-6">
            <a href="{{ route('kelas.index') }}" 
               class="w-full sm:w-auto text-center px-4 md:px-6 py-2.5 md:py-3 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition font-medium text-sm">
                Batal
            </a>
            <button type="submit" 
                    class="w-full sm:w-auto text-center bg-gradient-to-r from-cyan-500 to-blue-600 text-white px-4 md:px-6 py-2.5 md:py-3 rounded-lg font-medium hover:from-cyan-600 hover:to-blue-700 transition shadow-md shadow-cyan-200 text-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
                Simpan Kelas
            </button>
        </div>
    </form>
</div>
@endsection
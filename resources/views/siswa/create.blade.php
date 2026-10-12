@extends('layouts.app')

@section('title', 'Tambah Siswa')
@section('header', 'Tambah Siswa Baru')

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

    <form action="{{ route('siswa.store') }}" method="POST" class="space-y-4 md:space-y-6">
        @csrf

        {{-- 1. Data Siswa --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
            <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-3 md:mb-4 pb-2 border-b flex items-center gap-2">
                👤 Data Siswa
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Masukkan nama lengkap siswa"
                        class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition outline-none text-sm md:text-base">
                </div>
                <div class="md:col-span-2 p-3 bg-cyan-50 border border-cyan-200 rounded-lg text-sm text-cyan-800">
                        ℹ️ <b>Email & password dibuat otomatis.</b><br>
                        Contoh: nama <b>Aura Kasih</b> → email <b>aurakasih@siswa.id</b> · password <b>password</b><br>
                        Jika email sudah dipakai, sistem otomatis menambah angka (aurakasih2@siswa.id).
                    </div>
                    <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelas <span class="text-red-500">*</span></label>
                    <select name="kelas_id" required
                        class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition outline-none text-sm md:text-base bg-white">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- 2. Data Orang Tua / Wali --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
            <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-3 md:mb-4 pb-2 border-b flex items-center gap-2">
                👨‍👩‍👦 Data Orang Tua / Wali
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Orang Tua / Wali</label>
                    <input type="text" name="nama_ortu" value="{{ old('nama_ortu') }}" placeholder="Contoh: Bapak Ahmad Suryadi"
                        class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition outline-none text-sm md:text-base">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp Orang Tua</label>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-500 text-sm font-medium bg-gray-100 px-3 py-2.5 rounded-lg border border-gray-300">+62</span>
                        <!-- PERBAIKAN: type="tel" agar keyboard angka muncul otomatis di HP -->
                        <input type="tel" name="no_wa_ortu" value="{{ old('no_wa_ortu') }}" placeholder="81234567890"
                            class="flex-1 px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition outline-none text-sm md:text-base">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Format: 81234567890 (tanpa angka 0 di depan, tanpa spasi)</p>
                </div>
            </div>
        </div>

        {{-- 3. Tombol Aksi (Mobile Friendly) --}}
        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-2 pb-6">
            <a href="{{ route('siswa.index') }}" 
               class="w-full sm:w-auto text-center px-4 md:px-6 py-2.5 md:py-3 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition font-medium text-sm">
                Batal
            </a>
            <button type="submit" 
                    class="w-full sm:w-auto text-center bg-gradient-to-r from-cyan-500 to-blue-600 text-white px-4 md:px-6 py-2.5 md:py-3 rounded-lg font-medium hover:from-cyan-600 hover:to-blue-700 transition shadow-md shadow-cyan-200 text-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
                Simpan Siswa
            </button>
        </div>
    </form>
</div>
@endsection
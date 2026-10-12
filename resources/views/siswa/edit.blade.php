@extends('layouts.app')

@section('title', 'Edit Siswa')
@section('header', 'Edit Data Siswa')

@section('content')
<div class="max-w-3xl mx-auto">
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-lg">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <form action="{{ route('siswa.update', $siswa) }}" method="POST" class="space-y-6">
        @csrf @method('PUT')

        {{-- Data Siswa --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b"> Data Siswa</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ old('nama', $siswa->nama) }}" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $siswa->email) }}" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-xs text-gray-500">(kosongkan jika tidak ingin mengubah)</span></label>
                    <input type="password" name="password"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 outline-none">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                    <select name="kelas_id" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 outline-none">
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ old('kelas_id', $siswa->kelas_id) == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Data Orang Tua --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">👨👩‍👦 Data Orang Tua / Wali</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Orang Tua / Wali</label>
                    <input type="text" name="nama_ortu" value="{{ old('nama_ortu', $siswa->nama_ortu) }}"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 outline-none">
                </div>
                <div class="md:col-span-2">
    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp Orang Tua</label>
    <div class="flex items-center gap-2">
        <span class="text-gray-500 text-sm font-medium bg-gray-100 px-2 py-2.5 rounded-lg border border-gray-300">+62</span>
        <input type="text" name="no_wa_ortu" value="{{ old('no_wa_ortu', $siswa->no_wa_ortu) }}"
            placeholder="81234567890 atau 081234567890"
            class="flex-1 px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 outline-none">
    </div>
    <p class="text-xs text-gray-500 mt-1">
        💡 Sistem akan otomatis mengubah "08" menjadi "628" saat mengirim notifikasi.
    </p>
</div>

                <div class="md:col-span-2">
    <label class="block text-sm font-medium text-gray-700 mb-1">Peran / Role <span class="text-red-500">*</span></label>
    <select name="peran" required
        class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition outline-none text-sm md:text-base bg-white">
        @foreach($daftarPeran as $value => $label)
            <option value="{{ $value }}" {{ old('peran', $siswa->peran) === $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
    <p class="text-xs text-gray-500 mt-1">
        ⚠️ Mengubah peran akan mengubah hak akses pengguna ini di sistem.
    </p>
</div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('siswa.index') }}" class="px-6 py-2.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition font-medium text-sm">Batal</a>
            <button type="submit" class="bg-gradient-to-r from-cyan-500 to-blue-600 text-white px-6 py-2.5 rounded-lg font-medium hover:from-cyan-600 hover:to-blue-700 transition shadow-sm text-sm">
                 Update Siswa
            </button>
        </div>
    </form>
</div>
@endsection
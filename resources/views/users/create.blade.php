@extends('layouts.app')

@section('title', 'Buat User Baru')
@section('header', 'Buat User Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-4 md:space-y-6">

    @if($errors->any())
        <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-lg shadow-sm">
            <p class="font-semibold mb-1">Terdapat kesalahan pengisian:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.store') }}" method="POST" class="space-y-4 md:space-y-6">
        @csrf

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
            <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-4 pb-2 border-b flex items-center gap-2">
                👤 Data User Baru
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required 
                        class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition outline-none text-sm md:text-base">
                </div>
                
                <div class="md:col-span-2 p-3 bg-cyan-50 border border-cyan-200 rounded-lg text-sm text-cyan-800">
                        ℹ️ <b>Email & password dibuat otomatis.</b><br>
                        Contoh: nama <b>Aura Kasih</b> → email <b>aurakasih@siswa.id</b> · password <b>password</b><br>
                        Jika email sudah dipakai, sistem otomatis menambah angka (aurakasih2@siswa.id).
                    </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Peran / Role <span class="text-red-500">*</span></label>
                    <select name="peran" id="roleSelect" required onchange="toggleKelasField()"
                        class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition outline-none text-sm md:text-base bg-white">
                        <option value="">-- Pilih Peran --</option>
                        @foreach($daftarRole as $value => $label)
                            <option value="{{ $value }}" {{ old('peran') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2" id="kelasField" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelas <span class="text-xs text-gray-500">(Wajib untuk Siswa / Wali Kelas)</span></label>
                    <select name="kelas_id" 
                        class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition outline-none text-sm md:text-base bg-white">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIP / NIS</label>
                    <input type="text" name="nip" value="{{ old('nip') }}" placeholder="Opsional"
                        class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition outline-none text-sm md:text-base">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. HP / WhatsApp</label>
                    <input type="tel" name="no_hp" value="{{ old('no_hp') }}" placeholder="081234567890"
                        class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition outline-none text-sm md:text-base">
                </div>
            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-2 pb-6">
            <a href="{{ route('users.index') }}" 
                class="w-full sm:w-auto text-center px-4 md:px-6 py-2.5 md:py-3 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition font-medium text-sm">
                Batal
            </a>
            <button type="submit" 
                    class="w-full sm:w-auto text-center bg-gradient-to-r from-cyan-500 to-blue-600 text-white px-4 md:px-6 py-2.5 md:py-3 rounded-lg font-medium hover:from-cyan-600 hover:to-blue-700 transition shadow-md shadow-cyan-200 text-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Simpan User
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleKelasField() {
    const role = document.getElementById('roleSelect').value;
    const kelasField = document.getElementById('kelasField');
    
    // Tampilkan field kelas hanya untuk siswa dan wali kelas
    if (role === 'siswa' || role === 'wali_kelas') {
        kelasField.style.display = 'block';
    } else {
        kelasField.style.display = 'none';
    }
}

// Jalankan saat halaman load untuk cek nilai old()
document.addEventListener('DOMContentLoaded', function() {
    toggleKelasField();
});
</script>
@endpush
@endsection
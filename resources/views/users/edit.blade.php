@extends('layouts.app')

@section('title', 'Edit User')
@section('header', 'Edit User')

@section('content')
<div class="max-w-3xl mx-auto space-y-4 md:space-y-6">

    @if($errors->any())
        <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-lg shadow-sm">
            <p class="font-semibold mb-1">Terdapat kesalahan:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.update', $user->id) }}" method="POST" class="space-y-4 md:space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
            <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-4 pb-2 border-b flex items-center gap-2">
                👤 Data User
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required 
                        class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition outline-none text-sm md:text-base">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                        class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition outline-none text-sm md:text-base">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Peran / Role <span class="text-red-500">*</span></label>
                    <select name="peran" id="roleSelect" required onchange="toggleKelasField()"
                        class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition outline-none text-sm md:text-base bg-white">
                        @foreach($daftarRole as $value => $label)
                            <option value="{{ $value }}" {{ old('peran', $user->peran) === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">
                        ⚠️ Mengubah peran akan mengubah hak akses pengguna ini di sistem.
                    </p>
                </div>

                <div class="md:col-span-2" id="kelasField">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelas <span class="text-xs text-gray-500">(untuk siswa/wali kelas)</span></label>
                    <select name="kelas_id" 
                        class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition outline-none text-sm md:text-base bg-white">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ old('kelas_id', $user->kelas_id) == $k->id ? 'selected' : '' }}>
                                {{ $k->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                    <input type="text" name="nip" value="{{ old('nip', $user->nip) }}" placeholder="Opsional"
                        class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition outline-none text-sm md:text-base">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. HP / WhatsApp</label>
                    <input type="tel" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" placeholder="081234567890"
                        class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition outline-none text-sm md:text-base">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Password 
                        <span class="text-xs text-gray-500 font-normal">(kosongkan jika tidak ingin mengubah)</span>
                    </label>
                    <input type="password" name="password" placeholder="Minimal 6 karakter"
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Update User
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

// Jalankan saat halaman load
document.addEventListener('DOMContentLoaded', function() {
    toggleKelasField();
});
</script>
@endpush
@endsection
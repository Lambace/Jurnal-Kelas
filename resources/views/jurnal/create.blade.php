@extends('layouts.app')

@section('title', 'Isi Jurnal Baru')
@section('header', 'Isi Jurnal Harian')

@section('content')
    <div class="max-w-4xl mx-auto space-y-4 md:space-y-6">
        
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

        <form action="{{ route('jurnal.store') }}" method="POST" class="space-y-4 md:space-y-6">
            @csrf

            <!-- 1. Informasi Dasar -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-4 pb-2 border-b flex items-center gap-2">
                    📋 Informasi Dasar
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                            class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition outline-none text-sm md:text-base">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kelas <span class="text-red-500">*</span></label>
                        
                        @php
                            $user = auth()->user();
                            $isSiswa = $user->isSiswa();
                            $siswaKelas = $isSiswa ? $user->kelas : null;
                        @endphp

                        @if($isSiswa && !$siswaKelas)
                            <div class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-yellow-300 bg-yellow-50 text-yellow-800 text-sm flex items-start gap-2">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>Anda belum terdaftar di kelas manapun. Silakan hubungi Admin untuk di-assign ke kelas.</span>
                            </div>
                        @elseif($isSiswa && $siswaKelas)
                            <input type="text" value="{{ $siswaKelas->nama }}" readonly
                                class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 bg-gray-100 text-gray-700 cursor-not-allowed text-sm md:text-base">
                            <input type="hidden" name="id_kelas" value="{{ $siswaKelas->id }}">
                            <p class="text-xs text-gray-500 mt-1">Anda hanya dapat mengisi jurnal untuk kelas ini.</p>
                        @else
                            <select name="id_kelas" id="kelasSelect" required
                                class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition outline-none text-sm md:text-base bg-white">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" {{ old('id_kelas') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 2. Absensi Per Siswa (Interactive) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-purple-600 px-4 md:px-6 py-3 md:py-4">
                    <h3 class="text-base md:text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        Absensi Siswa Perindividu
                        <span class="text-xs font-normal opacity-90 ml-1 hidden sm:inline">(Klik tombol untuk mengubah status)</span>
                    </h3>
                </div>

                <div class="p-4 md:p-6">
                    <div id="absensiInfo" class="mb-4 p-3 bg-purple-50 rounded-lg text-sm text-gray-600 hidden">
                        Kelas: <span id="absensiKelasNama" class="font-bold text-purple-700"></span> — 
                        Total: <span id="absensiTotalSiswa" class="font-bold text-purple-600"></span> siswa
                    </div>

                    <div id="absensiPlaceholder" class="text-center py-8 text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <p class="text-sm">Pilih kelas terlebih dahulu untuk menampilkan daftar siswa</p>
                    </div>

                    <div id="daftarSiswaContainer" class="hidden">
                        <!-- Quick Actions -->
                        <div class="flex flex-wrap gap-2 mb-4 pb-4 border-b border-gray-200">
                            <span class="text-sm font-medium text-gray-600 self-center mr-1 w-full md:w-auto mb-1 md:mb-0">Tandai Semua:</span>
                            <button type="button" onclick="tandaiSemua('hadir')" class="px-3 py-1.5 text-xs font-medium bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition">✓ Hadir</button>
                            <button type="button" onclick="tandaiSemua('izin')" class="px-3 py-1.5 text-xs font-medium bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition">📝 Izin</button>
                            <button type="button" onclick="tandaiSemua('sakit')" class="px-3 py-1.5 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition">🤒 Sakit</button>
                            <button type="button" onclick="tandaiSemua('alpha')" class="px-3 py-1.5 text-xs font-medium bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">✗ Alpha</button>
                            <button type="button" onclick="tandaiSemua('terlambat')" class="px-3 py-1.5 text-xs font-medium bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 transition">⏰ Terlambat</button>
                            <button type="button" onclick="tandaiSemua('bolos')" class="px-3 py-1.5 text-xs font-medium bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition">🚫 Bolos</button>
                        </div>

                        <!-- Student List (Responsive Height) -->
                        <div id="daftarSiswa" class="space-y-2 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar"></div>

                        <!-- Auto Recap -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-xs font-medium text-gray-500 mb-2 flex items-center gap-1">
                                📊 REKAP OTOMATIS: <span class="text-[10px] bg-gray-200 text-gray-600 px-1.5 py-0.5 rounded">Tidak perlu diisi manual</span>
                            </p>
                            <div class="grid grid-cols-3 md:grid-cols-6 gap-2">
                                <div class="text-center p-2 bg-green-50 rounded-lg border border-green-200">
                                    <div class="text-xl font-bold text-green-600" id="rekapHadir">0</div>
                                    <div class="text-xs text-green-600 font-medium">Hadir</div>
                                </div>
                                <div class="text-center p-2 bg-blue-50 rounded-lg border border-blue-200">
                                    <div class="text-xl font-bold text-blue-600" id="rekapIzin">0</div>
                                    <div class="text-xs text-blue-600 font-medium">Izin</div>
                                </div>
                                <div class="text-center p-2 bg-yellow-50 rounded-lg border border-yellow-200">
                                    <div class="text-xl font-bold text-yellow-600" id="rekapSakit">0</div>
                                    <div class="text-xs text-yellow-600 font-medium">Sakit</div>
                                </div>
                                <div class="text-center p-2 bg-red-50 rounded-lg border border-red-200">
                                    <div class="text-xl font-bold text-red-600" id="rekapAlpha">0</div>
                                    <div class="text-xs text-red-600 font-medium">Alpha</div>
                                </div>
                                <div class="text-center p-2 bg-orange-50 rounded-lg border border-orange-200">
                                    <div class="text-xl font-bold text-orange-600" id="rekapTerlambat">0</div>
                                    <div class="text-xs text-orange-600 font-medium">Terlambat</div>
                                </div>
                                <div class="text-center p-2 bg-purple-50 rounded-lg border border-purple-200">
                                    <div class="text-xl font-bold text-purple-600" id="rekapBolos">0</div>
                                    <div class="text-xs text-purple-600 font-medium">Bolos</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="absensiHiddenInputs"></div>

            <!-- 3. Kondisi Kelas -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-4 pb-2 border-b flex items-center gap-2">
                    🏫 Kondisi Kelas
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kebersihan <span class="text-red-500">*</span></label>
                        <select name="kebersihan" required
                            class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 outline-none text-sm md:text-base bg-white">
                            <option value="sangat_bersih">🌟 Sangat Bersih</option>
                            <option value="bersih" selected>✅ Bersih</option>
                            <option value="kurang_bersih">⚠️ Kurang Bersih</option>
                            <option value="kotor">❌ Kotor</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Suasana Kelas</label>
                        <textarea name="suasana_kelas" rows="2" placeholder="Contoh: Kondusif, ramai, tenang..."
                            class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 outline-none text-sm md:text-base resize-none">{{ old('suasana_kelas') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Fasilitas (Rusak/Hilang)</label>
                        <textarea name="catatan_fasilitas" rows="2" placeholder="Contoh: Proyektor mati, AC bocor..."
                            class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 outline-none text-sm md:text-base resize-none">{{ old('catatan_fasilitas') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Pribadi Guru/Pengisi</label>
                        <textarea name="catatan_pribadi_guru" rows="3" placeholder="Catatan tambahan..."
                            class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 outline-none text-sm md:text-base resize-none">{{ old('catatan_pribadi_guru') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- 4. Submit Actions (Mobile Friendly) -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-2 pb-6">
                <a href="{{ route('jurnal.index') }}" class="w-full sm:w-auto text-center px-6 py-3 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition font-medium text-sm">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto text-center bg-gradient-to-r from-cyan-500 to-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:from-cyan-600 hover:to-blue-700 transition shadow-md shadow-cyan-200 text-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    Simpan Jurnal
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
    const siswaPerKelas = @json($siswaPerKelas->mapWithKeys(function($k) {
        return [$k->id => $k->users->map(fn($u) => ['id' => $u->id, 'nama' => $u->nama])];
    }));

    const STATUS_CYCLE = ['hadir', 'izin', 'sakit', 'alpha', 'terlambat', 'bolos'];
    const STATUS_STYLE = {
        hadir:     { bg: 'bg-green-500',  label: 'H', full: 'Hadir',     ring: 'ring-green-300' },
        izin:      { bg: 'bg-blue-500',   label: 'I', full: 'Izin',      ring: 'ring-blue-300' },
        sakit:     { bg: 'bg-yellow-500', label: 'S', full: 'Sakit',     ring: 'ring-yellow-300' },
        alpha:     { bg: 'bg-red-500',    label: 'A', full: 'Alpha',     ring: 'ring-red-300' },
        terlambat: { bg: 'bg-orange-500', label: 'T', full: 'Terlambat', ring: 'ring-orange-300' },
        bolos:     { bg: 'bg-purple-600', label: 'B', full: 'Bolos',     ring: 'ring-purple-300' },
    };

    let absensiState = {};

    function loadKelasData(kelasId, kelasNama) {
        if (!kelasId) {
            document.getElementById('absensiPlaceholder').classList.remove('hidden');
            document.getElementById('daftarSiswaContainer').classList.add('hidden');
            document.getElementById('absensiInfo').classList.add('hidden');
            absensiState = {};
            updateRekap();
            updateHiddenInputs();
            return;
        }

        const siswa = siswaPerKelas[kelasId] || [];
        document.getElementById('absensiPlaceholder').classList.add('hidden');
        document.getElementById('daftarSiswaContainer').classList.remove('hidden');
        document.getElementById('absensiInfo').classList.remove('hidden');
        document.getElementById('absensiKelasNama').textContent = kelasNama;
        document.getElementById('absensiTotalSiswa').textContent = siswa.length;

        absensiState = {};
        siswa.forEach(s => { absensiState[s.id] = { status: 'hadir', keterangan: '' }; });

        renderDaftarSiswa(siswa);
        updateRekap();
        updateHiddenInputs();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const hiddenKelasInput = document.querySelector('input[name="id_kelas"][type="hidden"]');
        
        if (hiddenKelasInput) {
            const kelasId = hiddenKelasInput.value;
            const kelasNama = hiddenKelasInput.previousElementSibling.value;
            loadKelasData(kelasId, kelasNama);
        } else {
            const kelasSelect = document.getElementById('kelasSelect');
            if (kelasSelect) {
                kelasSelect.addEventListener('change', function() {
                    const kelasId = this.value;
                    const kelasNama = this.options[this.selectedIndex]?.text || '';
                    loadKelasData(kelasId, kelasNama);
                });
                
                if (kelasSelect.value) {
                    kelasSelect.dispatchEvent(new Event('change'));
                }
            }
        }
    });

    // ⚠️ PERBAIKAN: Layout horizontal (flex-row) di SEMUA ukuran layar
    function renderDaftarSiswa(siswa) {
        const container = document.getElementById('daftarSiswa');
        container.innerHTML = '';
        siswa.forEach((s, index) => {
            const row = document.createElement('div');
            // Menggunakan flex-row agar tombol tetap di kanan, bahkan di layar kecil
            row.className = 'flex flex-row items-center gap-2 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition group';
            row.innerHTML = `
                <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-sm flex-shrink-0">${index + 1}</div>
                <div class="flex-1 min-w-0 mr-2">
                    <div class="font-medium text-gray-800 text-sm truncate">${s.nama}</div>
                    <input type="text" id="ket-${s.id}" placeholder="Keterangan (opsional)" value=""
                        onchange="updateKeterangan(${s.id}, this.value)"
                        class="mt-1 w-full text-xs px-2 py-1 rounded border border-gray-200 focus:border-purple-400 focus:ring-1 focus:ring-purple-200 outline-none">
                </div>
                <div class="flex flex-col items-center gap-1 flex-shrink-0">
                    <span id="label-${s.id}" class="text-[10px] font-medium text-gray-500">Hadir</span>
                    <button type="button" id="btn-${s.id}" onclick="cycleStatus(${s.id})"
                        class="w-9 h-9 rounded-full font-bold text-white text-xs shadow-md transition-all transform hover:scale-105 active:scale-95 flex items-center justify-center bg-green-500 ring-2 ring-green-300" title="Klik untuk ubah status">
                        H
                    </button>
                </div>
            `;
            container.appendChild(row);
        });
        Object.keys(absensiState).forEach(userId => applyButtonStyle(userId));
    }

    function cycleStatus(userId) {
        const current = absensiState[userId].status;
        const currentIndex = STATUS_CYCLE.indexOf(current);
        const nextIndex = (currentIndex + 1) % STATUS_CYCLE.length;
        absensiState[userId].status = STATUS_CYCLE[nextIndex];
        applyButtonStyle(userId);
        updateRekap();
        updateHiddenInputs();
    }

    // ⚠️ PERBAIKAN: Sesuaikan ukuran tombol dan label agar compact
    function applyButtonStyle(userId) {
        const status = absensiState[userId].status;
        const style = STATUS_STYLE[status];
        const btn = document.getElementById(`btn-${userId}`);
        const label = document.getElementById(`label-${userId}`);
        if (btn) {
            btn.className = `w-9 h-9 rounded-full font-bold text-white text-xs shadow-md transition-all transform hover:scale-105 active:scale-95 flex items-center justify-center ${style.bg} ring-2 ${style.ring}`;
            btn.textContent = style.label;
            btn.title = `Saat ini: ${style.full}. Klik untuk ubah.`;
        }
        if (label) {
            label.textContent = style.full;
            label.className = 'text-[10px] font-medium text-gray-500';
        }
    }

    function updateKeterangan(userId, value) {
        absensiState[userId].keterangan = value;
        updateHiddenInputs();
    }

    function tandaiSemua(status) {
        Object.keys(absensiState).forEach(userId => {
            absensiState[userId].status = status;
            applyButtonStyle(userId);
        });
        updateRekap();
        updateHiddenInputs();
    }

    function updateRekap() {
        const values = Object.values(absensiState);
        const counts = {
            hadir: values.filter(v => v.status === 'hadir').length,
            izin: values.filter(v => v.status === 'izin').length,
            sakit: values.filter(v => v.status === 'sakit').length,
            alpha: values.filter(v => v.status === 'alpha').length,
            terlambat: values.filter(v => v.status === 'terlambat').length,
            bolos: values.filter(v => v.status === 'bolos').length,
        };

        document.getElementById('rekapHadir').textContent = counts.hadir;
        document.getElementById('rekapIzin').textContent = counts.izin;
        document.getElementById('rekapSakit').textContent = counts.sakit;
        document.getElementById('rekapAlpha').textContent = counts.alpha;
        document.getElementById('rekapTerlambat').textContent = counts.terlambat;
        document.getElementById('rekapBolos').textContent = counts.bolos;
    }

    function updateHiddenInputs() {
        const container = document.getElementById('absensiHiddenInputs');
        container.innerHTML = '';
        Object.entries(absensiState).forEach(([userId, data], idx) => {
            ['user_id', 'status', 'keterangan'].forEach((field, i) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `absensi[${idx}][${field}]`;
                input.value = [userId, data.status, data.keterangan || ''][i];
                container.appendChild(input);
            });
        });
    }
    </script>
    @endpush
@endsection
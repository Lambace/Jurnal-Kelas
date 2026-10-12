@extends('layouts.app')

@section('title', 'Edit Jurnal')
@section('header', 'Edit Jurnal Harian')

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

        <form action="{{ route('jurnal.update', $jurnal->id) }}" method="POST" class="space-y-4 md:space-y-6">
            @csrf
            @method('PUT')

            <!-- 1. Informasi Dasar -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-4 pb-2 border-b flex items-center gap-2">
                    📋 Informasi Dasar
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', $jurnal->tanggal->format('Y-m-d')) }}" required
                            class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition outline-none text-sm md:text-base">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kelas <span class="text-red-500">*</span></label>
                        <select name="id_kelas" required
                            class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition outline-none text-sm md:text-base bg-white">
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}" {{ old('id_kelas', $jurnal->id_kelas) == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- 2. Daftar Siswa & Absensi -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-purple-600 px-4 md:px-6 py-3 md:py-4">
                    <h3 class="text-base md:text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        Absensi Siswa (Klik untuk ubah status)
                    </h3>
                </div>

                <div class="p-4 md:p-6">
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

                    <!-- Student List -->
                    <div class="space-y-2 max-h-[50vh] overflow-y-auto pr-2 custom-scrollbar">
                        @forelse($siswaDiKelas as $index => $siswa)
                            @php
                                $absensi = $absensiSudahAda->get($siswa->id);
                                $statusAwal = $absensi->status ?? 'hadir';
                                $keteranganAwal = $absensi->keterangan ?? '';
                            @endphp
                            
                            {{-- ⚠️ PERBAIKAN: Layout horizontal (flex-row) di SEMUA ukuran layar --}}
                            <div class="flex flex-row items-center gap-2 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition group">
                                <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-sm flex-shrink-0">{{ $index + 1 }}</div>
                                <div class="flex-1 min-w-0 mr-2">
                                    <div class="font-medium text-gray-800 text-sm truncate">{{ $siswa->nama }}</div>
                                    <input type="text" 
                                        id="ket-{{ $siswa->id }}" 
                                        placeholder="Keterangan (opsional)" 
                                        value="{{ old("absensi.$index.keterangan", $keteranganAwal) }}"
                                        onchange="updateKeterangan({{ $siswa->id }}, this.value)"
                                        class="mt-1 w-full text-xs px-2 py-1 rounded border border-gray-200 focus:border-purple-400 focus:ring-1 focus:ring-purple-200 outline-none">
                                </div>
                                <div class="flex flex-col items-center gap-1 flex-shrink-0">
                                    <span id="label-{{ $siswa->id }}" class="text-[10px] font-medium text-gray-500">{{ ucfirst($statusAwal) }}</span>
                                    <button type="button" 
                                        id="btn-{{ $siswa->id }}" 
                                        onclick="cycleStatus({{ $siswa->id }})"
                                        data-status="{{ old("absensi.$index.status", $statusAwal) }}"
                                        class="w-9 h-9 rounded-full font-bold text-white text-xs shadow-md transition-all transform hover:scale-105 active:scale-95 flex items-center justify-center 
                                            @if($statusAwal == 'hadir') bg-green-500 ring-2 ring-green-300
                                            @elseif($statusAwal == 'izin') bg-blue-500 ring-2 ring-blue-300
                                            @elseif($statusAwal == 'sakit') bg-yellow-500 ring-2 ring-yellow-300
                                            @elseif($statusAwal == 'alpha') bg-red-500 ring-2 ring-red-300
                                            @elseif($statusAwal == 'terlambat') bg-orange-500 ring-2 ring-orange-300
                                            @else bg-purple-600 ring-2 ring-purple-300 @endif" 
                                        title="Klik untuk ubah status">
                                        @if($statusAwal == 'hadir') H
                                        @elseif($statusAwal == 'izin') I
                                        @elseif($statusAwal == 'sakit') S
                                        @elseif($statusAwal == 'alpha') A
                                        @elseif($statusAwal == 'terlambat') T
                                        @else B @endif
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 text-sm py-8">Tidak ada siswa di kelas ini.</p>
                        @endforelse
                    </div>

                    <!-- Auto Recap -->
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-xs font-medium text-gray-500 mb-2 flex items-center gap-1">
                            📊 REKAP OTOMATIS: <span class="text-[10px] bg-gray-200 text-gray-600 px-1.5 py-0.5 rounded">Tidak perlu diisi manual</span>
                        </p>
                        <div class="grid grid-cols-3 md:grid-cols-6 gap-2">
                            <div class="text-center p-2 bg-green-50 rounded-lg border border-green-200">
                                <div class="text-xl font-bold text-green-600" id="rekapHadir">{{ $jurnal->kehadiran['hadir'] ?? 0 }}</div>
                                <div class="text-xs text-green-600 font-medium">Hadir</div>
                            </div>
                            <div class="text-center p-2 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="text-xl font-bold text-blue-600" id="rekapIzin">{{ $jurnal->kehadiran['izin'] ?? 0 }}</div>
                                <div class="text-xs text-blue-600 font-medium">Izin</div>
                            </div>
                            <div class="text-center p-2 bg-yellow-50 rounded-lg border border-yellow-200">
                                <div class="text-xl font-bold text-yellow-600" id="rekapSakit">{{ $jurnal->kehadiran['sakit'] ?? 0 }}</div>
                                <div class="text-xs text-yellow-600 font-medium">Sakit</div>
                            </div>
                            <div class="text-center p-2 bg-red-50 rounded-lg border border-red-200">
                                <div class="text-xl font-bold text-red-600" id="rekapAlpha">{{ $jurnal->kehadiran['alpha'] ?? 0 }}</div>
                                <div class="text-xs text-red-600 font-medium">Alpha</div>
                            </div>
                            <div class="text-center p-2 bg-orange-50 rounded-lg border border-orange-200">
                                <div class="text-xl font-bold text-orange-600" id="rekapTerlambat">{{ $jurnal->terlambat ?? 0 }}</div>
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
                            <option value="sangat_bersih" {{ old('kebersihan', $jurnal->kebersihan) == 'sangat_bersih' ? 'selected' : '' }}>🌟 Sangat Bersih</option>
                            <option value="bersih" {{ old('kebersihan', $jurnal->kebersihan) == 'bersih' ? 'selected' : '' }}>✅ Bersih</option>
                            <option value="kurang_bersih" {{ old('kebersihan', $jurnal->kebersihan) == 'kurang_bersih' ? 'selected' : '' }}>⚠️ Kurang Bersih</option>
                            <option value="kotor" {{ old('kebersihan', $jurnal->kebersihan) == 'kotor' ? 'selected' : '' }}>❌ Kotor</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Suasana Kelas</label>
                        <textarea name="suasana_kelas" rows="2" placeholder="Contoh: Kondusif, ramai, tenang..."
                            class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 outline-none text-sm md:text-base resize-none">{{ old('suasana_kelas', $jurnal->suasana_kelas) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Fasilitas (Rusak/Hilang)</label>
                        <textarea name="catatan_fasilitas" rows="2" placeholder="Contoh: Proyektor mati, AC bocor..."
                            class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 outline-none text-sm md:text-base resize-none">{{ old('catatan_fasilitas', $jurnal->catatan_fasilitas) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Pribadi Guru/Pengisi</label>
                        <textarea name="catatan_pribadi_guru" rows="3" placeholder="Catatan tambahan..."
                            class="w-full px-3 md:px-4 py-2.5 rounded-lg border border-gray-300 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 outline-none text-sm md:text-base resize-none">{{ old('catatan_pribadi_guru', $jurnal->catatan_pribadi_guru) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- 4. Submit Actions -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-2 pb-6">
                <a href="{{ route('jurnal.index') }}" class="w-full sm:w-auto text-center px-6 py-3 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition font-medium text-sm">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto text-center bg-gradient-to-r from-cyan-500 to-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:from-cyan-600 hover:to-blue-700 transition shadow-md shadow-cyan-200 text-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Update Jurnal
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
    const STATUS_CYCLE = ['hadir', 'izin', 'sakit', 'alpha', 'terlambat', 'bolos'];
    const STATUS_STYLE = {
        hadir:     { bg: 'bg-green-500',  label: 'H', full: 'Hadir',     ring: 'ring-green-300', text: 'text-green-600' },
        izin:      { bg: 'bg-blue-500',   label: 'I', full: 'Izin',      ring: 'ring-blue-300',  text: 'text-blue-600' },
        sakit:     { bg: 'bg-yellow-500', label: 'S', full: 'Sakit',     ring: 'ring-yellow-300', text: 'text-yellow-600' },
        alpha:     { bg: 'bg-red-500',    label: 'A', full: 'Alpha',     ring: 'ring-red-300',  text: 'text-red-600' },
        terlambat: { bg: 'bg-orange-500', label: 'T', full: 'Terlambat', ring: 'ring-orange-300', text: 'text-orange-600' },
        bolos:     { bg: 'bg-purple-600', label: 'B', full: 'Bolos',     ring: 'ring-purple-300', text: 'text-purple-600' },
    };

    let absensiState = {};

    // Inisialisasi state dari data yang sudah ada
    @foreach($siswaDiKelas as $index => $siswa)
        @php
            $absensi = $absensiSudahAda->get($siswa->id);
            $statusAwal = $absensi->status ?? 'hadir';
            $keteranganAwal = $absensi->keterangan ?? '';
        @endphp
        absensiState[{{ $siswa->id }}] = { 
            status: '{{ old("absensi.$index.status", $statusAwal) }}', 
            keterangan: '{{ old("absensi.$index.keterangan", $keteranganAwal) }}' 
        };
    @endforeach

    document.addEventListener('DOMContentLoaded', function() {
        updateRekap();
        updateHiddenInputs();
    });

    function cycleStatus(userId) {
        const current = absensiState[userId].status;
        const currentIndex = STATUS_CYCLE.indexOf(current);
        const nextIndex = (currentIndex + 1) % STATUS_CYCLE.length;
        absensiState[userId].status = STATUS_CYCLE[nextIndex];
        applyButtonStyle(userId);
        updateRekap();
        updateHiddenInputs();
    }

    // ⚠️ PERBAIKAN: Sesuaikan ukuran tombol dan label agar compact di mobile
    function applyButtonStyle(userId) {
        const status = absensiState[userId].status;
        const style = STATUS_STYLE[status];
        const btn = document.getElementById(`btn-${userId}`);
        const label = document.getElementById(`label-${userId}`);
        
        if (btn) {
            btn.className = `w-9 h-9 rounded-full font-bold text-white text-xs shadow-md transition-all transform hover:scale-105 active:scale-95 flex items-center justify-center ${style.bg} ring-2 ${style.ring}`;
            btn.textContent = style.label;
            btn.title = `Saat ini: ${style.full}. Klik untuk ubah.`;
            btn.setAttribute('data-status', status);
        }
        if (label) {
            label.textContent = style.full;
            label.className = `text-[10px] font-medium text-gray-500`;
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
@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')

    {{-- 🚨 NOTIFIKASI ADMIN: Ada User Baru Mendaftar --}}
    @if(auth()->user()->isAdmin() && ($jumlahTamu ?? 0) > 0)
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-r-lg shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 animate-pulse">
        <div class="flex items-start gap-3">
            <svg class="h-6 w-6 text-yellow-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div>
                <p class="font-bold text-yellow-800 text-base">
                    Ada {{ $jumlahTamu }} Pengguna Baru Menunggu Aktivasi!
                </p>
                <p class="text-sm text-yellow-700 mt-1">
                    User yang baru mendaftar belum memiliki akses penuh. Silakan tinjau dan ubah peran mereka.
                </p>
            </div>
        </div>
        <a href="{{ route('users.index') }}" class="w-full sm:w-auto text-center bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-5 rounded-lg text-sm transition shadow-sm flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            Lihat & Aktivasi
        </a>
    </div>
    @endif
    
    <!-- 1. Statistik Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6">
        <!-- Total Jurnal -->
        <div class="bg-white rounded-xl shadow-sm p-4 md:p-6 border-l-4 border-cyan-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500 font-medium">Total Jurnal</p>
                    <p class="text-2xl md:text-3xl font-bold text-gray-800 mt-1">{{ $totalJurnal }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-cyan-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Menunggu -->
        <div class="bg-white rounded-xl shadow-sm p-4 md:p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500 font-medium">Menunggu</p>
                    <p class="text-2xl md:text-3xl font-bold text-gray-800 mt-1">{{ $menunggu }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Disetujui -->
        <div class="bg-white rounded-xl shadow-sm p-4 md:p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500 font-medium">Disetujui</p>
                    <p class="text-2xl md:text-3xl font-bold text-gray-800 mt-1">{{ $disetujui }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Ditolak -->
        <div class="bg-white rounded-xl shadow-sm p-4 md:p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500 font-medium">Ditolak</p>
                    <p class="text-2xl md:text-3xl font-bold text-gray-800 mt-1">{{ $ditolak }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. GRAFIK KEHADIRAN DENGAN FILTER TANGGAL -->
    @if(auth()->user()->isWaliKelas() || auth()->user()->isAdmin())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6 mb-6 relative">
        
        {{-- Header & Form Filter --}}
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-4">
            <h3 class="text-base md:text-lg font-semibold text-gray-800 flex items-center gap-2 flex-shrink-0">
                📈 Tren Kehadiran Siswa
                @if(auth()->user()->isWaliKelas())
                    <span class="text-xs font-normal text-gray-500 bg-gray-100 px-2 py-1 rounded-full">Kelas yang Anda ampu</span>
                @endif
            </h3>

            {{-- 🆕 FORM FILTER TANGGAL - RESPONSIVE & RAPI --}}
            <form id="filterForm" class="w-full lg:w-auto">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_auto] gap-3 items-end">
                    <!-- Dari -->
                    <div class="flex flex-col gap-1">
                        <label for="startDate" class="text-xs font-medium text-gray-600">Dari:</label>
                        <input type="date" id="startDate" name="start_date" value="{{ $chartData['start_date'] }}"
                            class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-cyan-500 focus:ring-1 focus:ring-cyan-200 outline-none w-full bg-white">
                    </div>

                    <!-- Sampai -->
                    <div class="flex flex-col gap-1">
                        <label for="endDate" class="text-xs font-medium text-gray-600">Sampai:</label>
                        <input type="date" id="endDate" name="end_date" value="{{ $chartData['end_date'] }}"
                            class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-cyan-500 focus:ring-1 focus:ring-cyan-200 outline-none w-full bg-white">
                    </div>

                    <!-- Tombol Filter -->
                    <button type="submit" id="btnFilter"
                        class="px-4 py-2 text-sm font-medium bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg transition flex items-center justify-center gap-1.5 h-[38px] sm:col-span-2 lg:col-span-1 lg:h-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        <span>Filter</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Info Ringkasan --}}
        <div class="flex flex-wrap gap-3 mb-4 p-3 bg-gray-50 rounded-lg">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-green-500"></span>
                <span class="text-xs text-gray-600">Total Hadir: <strong id="summaryHadir" class="text-green-600">{{ $chartData['total_hadir'] ?? 0 }}</strong></span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-red-500"></span>
                <span class="text-xs text-gray-600">Total Tidak Hadir: <strong id="summaryTidakHadir" class="text-red-600">{{ $chartData['total_tidak_hadir'] ?? 0 }}</strong></span>
            </div>
            <div class="ml-auto text-xs text-gray-500" id="dateRangeInfo">
                {{ \Carbon\Carbon::parse($chartData['start_date'])->format('d M Y') }} - {{ \Carbon\Carbon::parse($chartData['end_date'])->format('d M Y') }}
            </div>
        </div>
        
        {{-- Canvas Grafik --}}
        <div class="relative h-64 md:h-80 w-full">
            <canvas id="attendanceLineChart"></canvas>
        </div>

        {{-- Loading Overlay --}}
        <div id="chartLoading" class="hidden absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center rounded-lg z-10">
            <div class="flex items-center gap-2 text-cyan-600">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm font-medium">Memuat data...</span>
            </div>
        </div>
    </div>
    @endif

    <!-- 3. Tabel Jurnal Terbaru -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="px-4 md:px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <h3 class="text-base md:text-lg font-semibold text-gray-800">Jurnal Terbaru</h3>
            <a href="{{ route('jurnal.index') }}" class="text-sm text-cyan-600 hover:underline flex-shrink-0">Lihat Semua →</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Tanggal</th>
                        <th class="px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Kelas</th>
                        <th class="px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap hidden sm:table-cell">Pengisi</th>
                        <th class="px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($jurnalTerbaru as $jurnal)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-3 md:px-6 py-3 md:py-4 text-sm text-gray-900 whitespace-nowrap">{{ $jurnal->tanggal->format('d M Y') }}</td>
                        <td class="px-3 md:px-6 py-3 md:py-4 text-sm text-gray-900 whitespace-nowrap">{{ $jurnal->kelas->nama ?? '-' }}</td>
                        <td class="px-3 md:px-6 py-3 md:py-4 text-sm text-gray-500 whitespace-nowrap hidden sm:table-cell">{{ $jurnal->pengisi->nama ?? '-' }}</td>
                        <td class="px-3 md:px-6 py-3 md:py-4 whitespace-nowrap">
                            @if($jurnal->status === 'disetujui')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Disetujui</span>
                            @elseif($jurnal->status === 'ditolak')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Ditolak</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Menunggu</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 md:px-6 py-8 text-center text-gray-400 text-sm">
                            @if(auth()->user()->isWaliKelas() && (auth()->user()->kelasWali()->count() === 0))
                                Anda belum ditugaskan sebagai wali kelas. Silakan hubungi Admin.
                            @else
                                Belum ada data jurnal.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
@if(auth()->user()->isWaliKelas() || auth()->user()->isAdmin())
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('attendanceLineChart');
        const filterForm = document.getElementById('filterForm');
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');
        const btnFilter = document.getElementById('btnFilter');
        const chartLoading = document.getElementById('chartLoading');
        const summaryHadir = document.getElementById('summaryHadir');
        const summaryTidakHadir = document.getElementById('summaryTidakHadir');
        const dateRangeInfo = document.getElementById('dateRangeInfo');

        if (!ctx) return;

        // Data awal dari server
        const initialData = @json($chartData);

        // Buat gradient
        const ctx2d = ctx.getContext('2d');
        const gradientHadir = ctx2d.createLinearGradient(0, 0, 0, 400);
        gradientHadir.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
        gradientHadir.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

        const gradientTidak = ctx2d.createLinearGradient(0, 0, 0, 400);
        gradientTidak.addColorStop(0, 'rgba(239, 68, 68, 0.3)');
        gradientTidak.addColorStop(1, 'rgba(239, 68, 68, 0.0)');

        // Inisialisasi Chart
        let attendanceChart = new Chart(ctx2d, {
            type: 'line',
            data: {
                labels: initialData.labels,
                datasets: [
                    {
                        label: 'Hadir',
                        data: initialData.hadir,
                        borderColor: '#10B981',
                        backgroundColor: gradientHadir,
                        borderWidth: 3,
                        pointBackgroundColor: '#10B981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Tidak Hadir',
                        data: initialData.tidak_hadir,
                        borderColor: '#EF4444',
                        backgroundColor: gradientTidak,
                        borderWidth: 3,
                        pointBackgroundColor: '#EF4444',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 20,
                            font: { size: 12 }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y + ' siswa';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false },
                        ticks: { font: { size: 11 }, color: '#6B7280' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false },
                        ticks: { font: { size: 11 }, color: '#6B7280', stepSize: 1, precision: 0 }
                    }
                }
            }
        });

        // ⚠️ FUNGSI: Update grafik via AJAX
        function updateChart(startDate, endDate) {
            if (new Date(startDate) > new Date(endDate)) {
                alert('Tanggal mulai tidak boleh lebih besar dari tanggal akhir!');
                return;
            }

            chartLoading.classList.remove('hidden');
            btnFilter.disabled = true;
            btnFilter.innerHTML = '<svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memuat...';

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                || document.querySelector('input[name="_token"]')?.value;

            fetch('{{ route("dashboard.attendanceData") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    start_date: startDate,
                    end_date: endDate
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }

                attendanceChart.data.labels = data.labels;
                attendanceChart.data.datasets[0].data = data.hadir;
                attendanceChart.data.datasets[1].data = data.tidak_hadir;
                attendanceChart.update();

                summaryHadir.textContent = data.total_hadir;
                summaryTidakHadir.textContent = data.total_tidak_hadir;
                dateRangeInfo.textContent = `${formatDate(startDate)} - ${formatDate(endDate)}`;

                if (data.labels.length === 0) {
                    ctx.parentElement.innerHTML = '<p class="text-center text-gray-500 text-sm py-8">Tidak ada data absensi pada rentang tanggal ini.</p>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal memuat data. Silakan coba lagi.');
            })
            .finally(() => {
                chartLoading.classList.add('hidden');
                btnFilter.disabled = false;
                btnFilter.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg> Filter';
            });
        }

        function formatDate(dateStr) {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const d = new Date(dateStr);
            return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
        }

        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            updateChart(startDateInput.value, endDateInput.value);
        });

        startDateInput.addEventListener('change', function() {
            if (endDateInput.value && endDateInput.value < this.value) {
                endDateInput.value = this.value;
            }
            endDateInput.min = this.value;
        });

        endDateInput.addEventListener('change', function() {
            startDateInput.max = this.value;
        });

        endDateInput.min = startDateInput.value;
        startDateInput.max = endDateInput.value;
    });
</script>
@endif
@endpush
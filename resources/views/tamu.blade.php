@extends('layouts.app')

@section('title', 'Tren Kehadiran Sekolah')
@section('header', 'Selamat Datang, ' . auth()->user()->nama . '!')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Banner Info --}}
    <div class="bg-gradient-to-r from-cyan-500 to-blue-600 rounded-xl p-5 md:p-6 text-white shadow-lg">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h2 class="text-lg md:text-xl font-bold mb-1">Akun Anda Sedang Dalam Proses Aktivasi</h2>
                <p class="text-sm md:text-base opacity-90">
                    Anda sedang melihat halaman preview. Administrator akan segera mengaktivasi akun Anda 
                    dan memberikan akses penuh sesuai peran (Guru / Wali Kelas / Siswa).
                </p>
            </div>
        </div>
    </div>

    {{-- Card Statistik --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        <div class="bg-white rounded-xl shadow-sm p-4 md:p-5 border-l-4 border-cyan-500">
            <p class="text-xs md:text-sm text-gray-500 font-medium">Total Kelas</p>
            <p class="text-2xl md:text-3xl font-bold text-gray-800 mt-1">{{ $daftarKelas->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 md:p-5 border-l-4 border-green-500">
            <p class="text-xs md:text-sm text-gray-500 font-medium">Total Hadir</p>
            <p class="text-2xl md:text-3xl font-bold text-green-600 mt-1">{{ $chartData['total_hadir'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 md:p-5 border-l-4 border-red-500">
            <p class="text-xs md:text-sm text-gray-500 font-medium">Total Tidak Hadir</p>
            <p class="text-2xl md:text-3xl font-bold text-red-600 mt-1">{{ $chartData['total_tidak_hadir'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 md:p-5 border-l-4 border-purple-500">
            <p class="text-xs md:text-sm text-gray-500 font-medium">Periode</p>
            <p class="text-sm md:text-base font-bold text-purple-600 mt-1">{{ \Carbon\Carbon::parse($chartData['start_date'])->format('d M') }} - {{ \Carbon\Carbon::parse($chartData['end_date'])->format('d M Y') }}</p>
        </div>
    </div>

    {{-- Grafik Tren Kehadiran --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6 relative">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-4">
            <h3 class="text-base md:text-lg font-semibold text-gray-800 flex items-center gap-2">
                 Tren Kehadiran Seluruh Kelas
                <span class="text-xs font-normal text-gray-500 bg-gray-100 px-2 py-1 rounded-full">Seluruh Sekolah</span>
            </h3>

            <form id="filterForm" class="w-full lg:w-auto">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_auto] gap-3 items-end">
                    <div class="flex flex-col gap-1">
                        <label for="startDate" class="text-xs font-medium text-gray-600">Dari:</label>
                        <input type="date" id="startDate" name="start_date" value="{{ $chartData['start_date'] }}"
                            class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-cyan-500 focus:ring-1 focus:ring-cyan-200 outline-none w-full bg-white">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="endDate" class="text-xs font-medium text-gray-600">Sampai:</label>
                        <input type="date" id="endDate" name="end_date" value="{{ $chartData['end_date'] }}"
                            class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-cyan-500 focus:ring-1 focus:ring-cyan-200 outline-none w-full bg-white">
                    </div>
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

        <div class="flex flex-wrap gap-3 mb-4 p-3 bg-gray-50 rounded-lg">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-green-500"></span>
                <span class="text-xs text-gray-600">Total Hadir: <strong id="summaryHadir" class="text-green-600">{{ $chartData['total_hadir'] }}</strong></span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-red-500"></span>
                <span class="text-xs text-gray-600">Total Tidak Hadir: <strong id="summaryTidakHadir" class="text-red-600">{{ $chartData['total_tidak_hadir'] }}</strong></span>
            </div>
        </div>

        <div class="relative h-64 md:h-80 w-full">
            <canvas id="attendanceLineChart"></canvas>
        </div>

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

    {{-- Daftar Kelas --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-4">📚 Daftar Kelas di Sekolah</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
            @forelse($daftarKelas as $kelas)
                <div class="bg-gradient-to-br from-cyan-50 to-blue-50 border border-cyan-200 rounded-lg p-3 text-center">
                    <div class="text-lg font-bold text-cyan-700">{{ $kelas->nama }}</div>
                </div>
            @empty
                <p class="text-gray-500 text-sm col-span-full text-center py-4">Belum ada kelas yang terdaftar.</p>
            @endforelse
        </div>
    </div>

    {{-- Tombol Logout --}}
    <div class="flex justify-center pb-6">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition text-sm">
                Logout
            </button>
        </form>
    </div>
</div>

@push('scripts')
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

        if (!ctx) return;

        const initialData = @json($chartData);
        const ctx2d = ctx.getContext('2d');

        const gradientHadir = ctx2d.createLinearGradient(0, 0, 0, 400);
        gradientHadir.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
        gradientHadir.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

        const gradientTidak = ctx2d.createLinearGradient(0, 0, 0, 400);
        gradientTidak.addColorStop(0, 'rgba(239, 68, 68, 0.3)');
        gradientTidak.addColorStop(1, 'rgba(239, 68, 68, 0.0)');

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
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top', align: 'end',
                        labels: { usePointStyle: true, pointStyle: 'circle', padding: 20, font: { size: 12 } }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)', padding: 12, cornerRadius: 8,
                        callbacks: { label: function(context) { return context.dataset.label + ': ' + context.parsed.y + ' siswa'; } }
                    }
                },
                scales: {
                    x: { grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false }, ticks: { font: { size: 11 }, color: '#6B7280' } },
                    y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false }, ticks: { font: { size: 11 }, color: '#6B7280', stepSize: 1, precision: 0 } }
                }
            }
        });

        function updateChart(startDate, endDate) {
            if (new Date(startDate) > new Date(endDate)) {
                alert('Tanggal mulai tidak boleh lebih besar dari tanggal akhir!');
                return;
            }

            chartLoading.classList.remove('hidden');
            btnFilter.disabled = true;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                || document.querySelector('input[name="_token"]')?.value;

            fetch('{{ route("tamu.attendanceData") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ start_date: startDate, end_date: endDate })
            })
            .then(response => response.json())
            .then(data => {
                attendanceChart.data.labels = data.labels;
                attendanceChart.data.datasets[0].data = data.hadir;
                attendanceChart.data.datasets[1].data = data.tidak_hadir;
                attendanceChart.update();

                summaryHadir.textContent = data.total_hadir;
                summaryTidakHadir.textContent = data.total_tidak_hadir;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal memuat data.');
            })
            .finally(() => {
                chartLoading.classList.add('hidden');
                btnFilter.disabled = false;
            });
        }

        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            updateChart(startDateInput.value, endDateInput.value);
        });

        startDateInput.addEventListener('change', function() {
            if (endDateInput.value && endDateInput.value < this.value) endDateInput.value = this.value;
            endDateInput.min = this.value;
        });

        endDateInput.addEventListener('change', function() {
            startDateInput.max = this.value;
        });

        endDateInput.min = startDateInput.value;
        startDateInput.max = endDateInput.value;
    });
</script>
@endpush
@endsection

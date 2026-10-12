<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="theme-color" content="#0f172a">
    <title>@yield('title', 'Dashboard') - Jurnal Kelas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased overflow-x-hidden">

    <div class="flex h-screen overflow-hidden">

        <!-- OVERLAY MOBILE -->
        <div id="sidebarOverlay" onclick="toggleSidebar()"
             class="fixed inset-0 bg-black/50 backdrop-blur-sm z-20 hidden md:hidden opacity-0 transition-opacity duration-300"></div>

        <!-- SIDEBAR -->
        <aside id="sidebar" class="w-64 bg-slate-900 text-white flex flex-col fixed inset-y-0 left-0 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out z-30">
            
            <div class="p-4 md:p-6 border-b border-slate-700 flex items-center justify-between">
                <div>
                    <h1 class="text-xl md:text-2xl font-bold">Jurnal<span class="text-cyan-400">Kelas</span></h1>
                    <p class="text-xs text-slate-400 mt-1 capitalize">{{ Auth::user()->peran ?? '' }}</p>
                </div>
                <button onclick="toggleSidebar()" class="md:hidden text-slate-400 hover:text-white transition p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <nav class="flex-1 p-3 md:p-4 space-y-1 overflow-y-auto">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 md:px-4 py-2.5 md:py-3 rounded-lg transition text-sm md:text-base {{ request()->routeIs('dashboard') ? 'bg-cyan-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </a>

               @if(in_array(Auth::user()->peran, ['guru', 'wali_kelas', 'siswa', 'admin']))
                <a href="{{ route('jurnal.index') }}" 
                   class="flex items-center gap-3 px-3 md:px-4 py-2.5 md:py-3 rounded-lg transition text-sm md:text-base {{ request()->routeIs('jurnal.*') ? 'bg-cyan-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span>Jurnal Harian</span>
                </a>
                @endif
                {{-- Laporan Kehadiran (Khusus Wali Kelas & Admin) --}}
                    @if(auth()->user()->isWaliKelas() || auth()->user()->isAdmin())
                    <a href="{{ route('laporan.kehadiran') }}"
                    class="flex items-center gap-3 px-3 md:px-4 py-2.5 md:py-3 rounded-lg transition text-sm md:text-base {{ request()->routeIs('laporan.*') ? 'bg-cyan-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Laporan Kehadiran</span>
                    </a>
                    @endif
                

                @if(in_array(Auth::user()->peran, ['admin', 'wali_kelas']))
                <a href="{{ route('siswa.index') }}" class="flex items-center gap-3 px-3 md:px-4 py-2.5 md:py-3 rounded-lg transition text-sm md:text-base {{ request()->routeIs('siswa.*') ? 'bg-cyan-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Data Siswa</span>
                </a>
                @endif

                @if(Auth::user()->peran === 'admin')
                    <a href="{{ route('guru.index') }}" class="flex items-center gap-3 px-3 md:px-4 py-2.5 md:py-3 rounded-lg transition text-sm md:text-base {{ request()->routeIs('guru.*') ? 'bg-cyan-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Data Guru</span>
                    </a>
                    <a href="{{ route('kelas.index') }}" class="flex items-center gap-3 px-3 md:px-4 py-2.5 md:py-3 rounded-lg transition text-sm md:text-base {{ request()->routeIs('kelas.*') ? 'bg-cyan-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span>Manajemen Kelas</span>
                    </a>
                @endif

             {{-- Manajemen User - Hanya untuk Admin --}}

                @if(auth()->user()->isAdmin())
                    @php
                        // Hitung jumlah tamu untuk badge di sidebar
                        $jumlahTamuSidebar = \App\Models\User::where('peran', 'tamu')->count();
                    @endphp

                    <a href="{{ route('users.index') }}"
                    class="flex items-center gap-3 px-3 md:px-4 py-2.5 md:py-3 rounded-lg transition text-sm md:text-base {{ request()->routeIs('users.*') ? 'bg-cyan-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span>Manajemen User</span>
                        
                        {{-- 🚨 BADGE NOTIFIKASI --}}
                        @if($jumlahTamuSidebar > 0)
                            <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full animate-bounce">
                                {{ $jumlahTamuSidebar }}
                            </span>
                        @endif
                    </a>
                @endif

            </nav>

            <div class="p-3 md:p-4 border-t border-slate-700">
                <div class="flex items-center gap-3 mb-3 px-2">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                        {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->nama }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 w-full px-3 md:px-4 py-2.5 text-red-400 hover:bg-slate-800 rounded-lg transition text-sm">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <div class="flex-1 flex flex-col md:ml-64 min-w-0">
            <header class="bg-white shadow-sm border-b border-gray-200 px-4 md:px-6 py-3 md:py-4 flex justify-between items-center sticky top-0 z-10">
                <div class="flex items-center gap-3 md:gap-4 min-w-0 flex-1">
                    <button onclick="toggleSidebar()" class="md:hidden text-gray-500 hover:text-gray-700 transition flex-shrink-0 p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h2 class="text-base md:text-xl font-semibold text-gray-800 truncate">@yield('header', 'Dashboard')</h2>
                </div>
                <div class="flex items-center gap-2 md:gap-3 flex-shrink-0">
                    <span class="text-xs md:text-sm text-gray-600 hidden sm:block truncate max-w-[120px] md:max-w-none">{{ Auth::user()->nama }}</span>
                    <div class="w-8 h-8 md:w-9 md:h-9 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-white font-bold text-xs md:text-sm flex-shrink-0">
                        {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
                    </div>
                </div>
            </header>

            <div class="px-4 md:px-6">
                @if(session('success'))
                    <div class="mt-3 md:mt-4 p-3 md:p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-lg flex items-start gap-2 animate-fade-in">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm md:text-base">{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mt-3 md:mt-4 p-3 md:p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg flex items-start gap-2 animate-fade-in">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span class="text-sm md:text-base">{{ session('error') }}</span>
                    </div>
                @endif
            </div>

            <main class="flex-1 overflow-y-auto p-4 md:p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const isMobile = window.innerWidth < 768;
            const isOpen = !sidebar.classList.contains('-translate-x-full');
            
            if (isMobile) {
                if (isOpen) {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('opacity-0');
                    setTimeout(() => overlay.classList.add('hidden'), 300);
                    document.body.style.overflow = '';
                } else {
                    overlay.classList.remove('hidden');
                    void overlay.offsetWidth;
                    overlay.classList.remove('opacity-0');
                    sidebar.classList.remove('-translate-x-full');
                    document.body.style.overflow = 'hidden';
                }
            }
        }

        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (window.innerWidth >= 768) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.add('hidden');
                overlay.classList.add('opacity-0');
                document.body.style.overflow = '';
            } else {
                sidebar.classList.add('-translate-x-full');
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && window.innerWidth < 768) {
                const sidebar = document.getElementById('sidebar');
                if (!sidebar.classList.contains('-translate-x-full')) toggleSidebar();
            }
        });
    </script>

    <style>
        @keyframes fade-in { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fade-in 0.3s ease-out; }
        #sidebar nav::-webkit-scrollbar { width: 4px; }
        #sidebar nav::-webkit-scrollbar-thumb { background: #334155; border-radius: 2px; }
        @supports (-webkit-touch-callout: none) { .h-screen { height: -webkit-fill-available; } }
    </style>
</body>
</html>
<?php

namespace App\Http\Controllers;

use App\Models\JurnalHarian;
use App\Models\Kelas;
use App\Models\AbsensiHarian;
use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; 
use App\Services\WhatsAppService;

class JurnalController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $jurnals = JurnalHarian::with(['kelas', 'pengisi', 'penyetuju'])
                ->latest('tanggal')
                ->paginate(10);
                
        } elseif ($user->isWaliKelas()) {
            $kelasIds = \App\Models\Kelas::where('wali_kelas_id', $user->id)->pluck('id');
            
            if ($kelasIds->isEmpty()) {
                return view('jurnal.index', [
                    'jurnals' => collect([]),
                    'belumDiAssign' => true
                ]);
            }
            
            $jurnals = JurnalHarian::with(['kelas', 'pengisi', 'penyetuju'])
                ->whereIn('id_kelas', $kelasIds)
                ->latest('tanggal')
                ->paginate(10);
                
        } else {
            $jurnals = JurnalHarian::with(['kelas', 'pengisi', 'penyetuju'])
                ->where('id_pengisi', $user->id)
                ->latest('tanggal')
                ->paginate(10);
        }

        return view('jurnal.index', compact('jurnals'));
    }

    public function create()
    {
        $user = auth()->user();
        
        if ($user->isWaliKelas()) {
            $kelas = $user->kelasWali()->get();
        } elseif ($user->isSiswa()) {
            $kelas = $user->kelas_id 
                ? Kelas::where('id', $user->kelas_id)->get() 
                : collect([]);
        } else {
            $kelas = Kelas::all();
        }

        $siswaPerKelas = Kelas::with(['users' => function($q) {
            $q->where('peran', 'siswa')->orderBy('nama');
        }])->get();
        
        return view('jurnal.create', compact('kelas', 'siswaPerKelas'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->isWaliKelas()) {
            $allowedKelasIds = $user->kelasWali()->pluck('id');
        } elseif ($user->isSiswa()) {
            $allowedKelasIds = $user->kelas_id 
                ? collect([$user->kelas_id]) 
                : collect([]);
        } else {
            $allowedKelasIds = Kelas::pluck('id');
        }

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'id_kelas' => ['required', Rule::in($allowedKelasIds)],
            'cuaca' => 'nullable|string',
            'kebersihan' => 'required|string',
            'suhu_ruangan' => 'nullable|string',
            'catatan_fasilitas' => 'nullable|string',
            'mata_pelajaran' => 'nullable|string',
            'topik_materi' => 'nullable|string',
            'metode_pembelajaran' => 'nullable|string',
            'hambatan_belajar' => 'nullable|string',
            'kejadian_positif' => 'nullable|string',
            'pelanggaran' => 'nullable|string',
            'siswa_bermasalah' => 'nullable|string',
            'catatan_pribadi_guru' => 'nullable|string',
            'catatan_ketidakhadiran' => 'nullable|string',
            'absensi' => 'nullable|array',
            'absensi.*.user_id' => 'nullable|exists:users,id',
            'absensi.*.status' => 'nullable|in:hadir,izin,sakit,alpha,terlambat,bolos',
            'absensi.*.keterangan' => 'nullable|string|max:255',
        ]);

        // ⚠️ KODE PENYIMPANAN SUDAH LENGKAP DI SINI (Tanpa komentar pengganggu)
        $absensiData = collect($validated['absensi'] ?? []);
        $validAbsensi = $absensiData->filter(function ($item) {
            return !empty($item['user_id']) && !empty($item['status']);
        });

        $rekapKehadiran = [
            'hadir' => $validAbsensi->where('status', 'hadir')->count(),
            'sakit' => $validAbsensi->where('status', 'sakit')->count(),
            'izin' => $validAbsensi->where('status', 'izin')->count(),
            'alpha' => $validAbsensi->where('status', 'alpha')->count(),
        ];
        
        $terlambat = $validAbsensi->where('status', 'terlambat')->count();

        $jurnal = JurnalHarian::create([
            'tanggal' => $validated['tanggal'],
            'id_kelas' => $validated['id_kelas'],
            'id_pengisi' => $user->id,
            'peran_pengisi' => $user->peran,
            'cuaca' => $validated['cuaca'] ?? null,
            'kehadiran' => $rekapKehadiran,
            'terlambat' => $terlambat,
            'kebersihan' => $validated['kebersihan'],
            'suhu_ruangan' => $validated['suhu_ruangan'] ?? null,
            'catatan_fasilitas' => $validated['catatan_fasilitas'] ?? null,
            'mata_pelajaran' => $validated['mata_pelajaran'] ?? null,
            'topik_materi' => $validated['topik_materi'] ?? null,
            'metode_pembelajaran' => $validated['metode_pembelajaran'] ?? null,
            'hambatan_belajar' => $validated['hambatan_belajar'] ?? null,
            'kejadian_positif' => $validated['kejadian_positif'] ?? null,
            'pelanggaran' => $validated['pelanggaran'] ?? null,
            'siswa_bermasalah' => $validated['siswa_bermasalah'] ?? null,
            'catatan_pribadi_guru' => $validated['catatan_pribadi_guru'] ?? null,
            'catatan_ketidakhadiran' => $validated['catatan_ketidakhadiran'] ?? null,
            'status' => 'menunggu_verifikasi',
        ]);

        if ($validAbsensi->isNotEmpty()) {
            foreach ($validAbsensi as $item) {
                AbsensiHarian::create([
                    'jurnal_id' => $jurnal->id,
                    'user_id' => $item['user_id'],
                    'kelas_id' => $validated['id_kelas'],
                    'tanggal' => $validated['tanggal'],
                    'status' => $item['status'],
                    'keterangan' => $item['keterangan'] ?? null,
                ]);

                // ️ LOGIKA KIRIM NOTIFIKASI WA
                $statusPerluNotif = ['sakit', 'izin', 'alpha', 'bolos', 'terlambat'];
                if (in_array($item['status'], $statusPerluNotif)) {
                    $siswa = User::find($item['user_id']);
                    if ($siswa && !empty($siswa->no_wa_ortu)) {
                        // Panggil service WhatsApp
                        WhatsAppService::sendNotification(
                            $siswa->no_wa_ortu,
                            $siswa->nama,
                            $item['status'],
                            $validated['tanggal'],
                            $item['keterangan'] ?? '-'
                        );
                    }
                }
            }
        }

        return redirect()->route('jurnal.index')->with('success', 'Jurnal dan absensi berhasil disimpan!');
    }

    public function show(JurnalHarian $jurnal)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin() && $jurnal->id_pengisi !== $user->id && $jurnal->id_kelas !== $user->kelasWali()->first()?->id) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk melihat jurnal ini.');
        }

        $jurnal->load(['kelas', 'pengisi', 'penyetuju', 'absensi.siswa']);
        return view('jurnal.show', compact('jurnal'));
    }

    public function edit(JurnalHarian $jurnal)
    {
        if ($jurnal->status !== 'menunggu_verifikasi') {
            return redirect()->route('jurnal.index')->with('error', 'Jurnal sudah diverifikasi, tidak bisa diedit.');
        }

        $user = auth()->user();
        
        if ($user->isWaliKelas()) {
            $kelas = $user->kelasWali()->get();
        } elseif ($user->isSiswa()) {
            $kelas = $user->kelas_id 
                ? Kelas::where('id', $user->kelas_id)->get() 
                : collect([]);
        } else {
            $kelas = Kelas::all();
        }

        // ⚠️ PERBAIKAN: Ambil daftar siswa di kelas jurnal ini
        $siswaDiKelas = User::where('peran', 'siswa')
            ->where('kelas_id', $jurnal->id_kelas)
            ->orderBy('nama')
            ->get();

        // ⚠️ PERBAIKAN: Ambil data absensi yang sudah ada untuk pre-fill form
        $absensiSudahAda = $jurnal->absensi()->get()->keyBy('user_id');

        $siswaPerKelas = Kelas::with(['users' => function($q) {
            $q->where('peran', 'siswa')->orderBy('nama');
        }])->get();

        return view('jurnal.edit', compact('jurnal', 'kelas', 'siswaPerKelas', 'siswaDiKelas', 'absensiSudahAda'));
    }

    public function update(Request $request, JurnalHarian $jurnal)
    {
        if ($jurnal->status !== 'menunggu_verifikasi') {
            return redirect()->route('jurnal.index')->with('error', 'Jurnal sudah diverifikasi.');
        }

        $user = auth()->user();
        
        if (!$user->isAdmin() && $jurnal->id_pengisi !== $user->id) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk memperbarui jurnal ini.');
        }

        $allowedKelasIds = $user->isWaliKelas() ? $user->kelasWali()->pluck('id') : Kelas::pluck('id');

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'id_kelas' => ['required', Rule::in($allowedKelasIds)],
            'kebersihan' => 'required|string',
            'suhu_ruangan' => 'nullable|string',
            'catatan_fasilitas' => 'nullable|string',
            'mata_pelajaran' => 'nullable|string',
            'topik_materi' => 'nullable|string',
            'metode_pembelajaran' => 'nullable|string',
            'hambatan_belajar' => 'nullable|string',
            'kejadian_positif' => 'nullable|string',
            'pelanggaran' => 'nullable|string',
            'siswa_bermasalah' => 'nullable|string',
            'catatan_pribadi_guru' => 'nullable|string',
            'catatan_ketidakhadiran' => 'nullable|string',
            // ⚠️ PERBAIKAN: Tambahkan validasi untuk array absensi
            'absensi' => 'nullable|array',
            'absensi.*.user_id' => 'nullable|exists:users,id',
            'absensi.*.status' => 'nullable|in:hadir,izin,sakit,alpha,terlambat,bolos',
            'absensi.*.keterangan' => 'nullable|string|max:255',
        ]);

        // 1. Update data utama jurnal
        $jurnal->update([
            'tanggal' => $validated['tanggal'],
            'id_kelas' => $validated['id_kelas'],
            'kebersihan' => $validated['kebersihan'],
            'suhu_ruangan' => $validated['suhu_ruangan'] ?? null,
            'catatan_fasilitas' => $validated['catatan_fasilitas'] ?? null,
            'mata_pelajaran' => $validated['mata_pelajaran'] ?? null,
            'topik_materi' => $validated['topik_materi'] ?? null,
            'metode_pembelajaran' => $validated['metode_pembelajaran'] ?? null,
            'hambatan_belajar' => $validated['hambatan_belajar'] ?? null,
            'kejadian_positif' => $validated['kejadian_positif'] ?? null,
            'pelanggaran' => $validated['pelanggaran'] ?? null,
            'siswa_bermasalah' => $validated['siswa_bermasalah'] ?? null,
            'catatan_pribadi_guru' => $validated['catatan_pribadi_guru'] ?? null,
            'catatan_ketidakhadiran' => $validated['catatan_ketidakhadiran'] ?? null,
        ]);

        // 2. ⚠️ PERBAIKAN: Handle update data absensi
        $absensiData = collect($validated['absensi'] ?? []);
        $validAbsensi = $absensiData->filter(function ($item) {
            return !empty($item['user_id']) && !empty($item['status']);
        });

        // Hapus absensi lama
        $jurnal->absensi()->delete();

        // Buat absensi baru berdasarkan input terbaru
        if ($validAbsensi->isNotEmpty()) {
            foreach ($validAbsensi as $item) {
                AbsensiHarian::create([
                    'jurnal_id' => $jurnal->id,
                    'user_id' => $item['user_id'],
                    'kelas_id' => $validated['id_kelas'],
                    'tanggal' => $validated['tanggal'],
                    'status' => $item['status'],
                    'keterangan' => $item['keterangan'] ?? null,
                ]);

                // ️ LOGIKA KIRIM NOTIFIKASI WA
                $statusPerluNotif = ['sakit', 'izin', 'alpha', 'bolos', 'terlambat'];
                if (in_array($item['status'], $statusPerluNotif)) {
                    $siswa = User::find($item['user_id']);
                    if ($siswa && !empty($siswa->no_wa_ortu)) {
                        // Panggil service WhatsApp
                        WhatsAppService::sendNotification(
                            $siswa->no_wa_ortu,
                            $siswa->nama,
                            $item['status'],
                            $validated['tanggal'],
                            $item['keterangan'] ?? '-'
                        );
                    }
                }
            }
        }

        // 3. ⚠️ PERBAIKAN: Update ulang rekap kehadiran di tabel jurnal
        $rekapKehadiran = [
            'hadir' => $validAbsensi->where('status', 'hadir')->count(),
            'sakit' => $validAbsensi->where('status', 'sakit')->count(),
            'izin' => $validAbsensi->where('status', 'izin')->count(),
            'alpha' => $validAbsensi->where('status', 'alpha')->count(),
        ];
        $terlambat = $validAbsensi->where('status', 'terlambat')->count();

        $jurnal->update([
            'kehadiran' => $rekapKehadiran,
            'terlambat' => $terlambat,
        ]);

        return redirect()->route('jurnal.index')->with('success', 'Jurnal dan data absensi berhasil diperbarui!');
    }

    public function destroy(JurnalHarian $jurnal)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin() && $jurnal->id_pengisi !== $user->id) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk menghapus jurnal ini.');
        }

        if ($jurnal->status !== 'menunggu_verifikasi') {
            return redirect()->route('jurnal.index')->with('error', 'Tidak bisa menghapus jurnal yang sudah diverifikasi.');
        }

        $jurnal->delete();
        return redirect()->route('jurnal.index')->with('success', 'Jurnal berhasil dihapus.');
    }

    public function verifikasi(Request $request, JurnalHarian $jurnal)
    {
        $user = auth()->user();

        if (!in_array($user->peran, ['admin', 'wali_kelas'])) {
            abort(403, 'Anda tidak memiliki izin.');
        }

        if ($user->isWaliKelas() && $jurnal->id_kelas !== $user->kelasWali()->first()?->id) {
            abort(403, 'Akses Ditolak. Anda hanya dapat memverifikasi jurnal dari kelas yang Anda ampu.');
        }

        $validated = $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan_penolakan' => 'nullable|string|required_if:status,ditolak',
        ]);

        $jurnal->update([
            'status' => $validated['status'],
            'id_penyetuju' => $user->id,
            'disetujui_pada' => now(),
            'catatan_penolakan' => $validated['catatan_penolakan'] ?? null,
        ]);

        return redirect()->route('jurnal.show', $jurnal)->with('success', 'Status jurnal berhasil diperbarui!');
    }
}
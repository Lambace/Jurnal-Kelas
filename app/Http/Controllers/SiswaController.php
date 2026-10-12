<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; 
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SiswaExport;
use App\Imports\SiswaImport;

class SiswaController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            $siswa = User::where('peran', 'siswa')->with('kelas')->orderBy('kelas_id')->orderBy('nama')->paginate(15);
        } else {
            $kelasIds = $user->kelasWali()->pluck('id');
            $siswa = User::where('peran', 'siswa')->whereIn('kelas_id', $kelasIds)->with('kelas')->orderBy('nama')->paginate(15);
        }

        return view('siswa.index', compact('siswa'));
    }

    public function create()
    {
        $user = auth()->user();
        $kelas = $user->isAdmin() ? Kelas::all() : $user->kelasWali()->get();
        return view('siswa.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $allowedKelasIds = $user->isAdmin() ? Kelas::pluck('id') : $user->kelasWali()->pluck('id');

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'kelas_id' => ['required', Rule::in($allowedKelasIds)],
            'nama_ortu' => 'nullable|string|max:255',
            'no_wa_ortu' => 'nullable|string|max:20|regex:/^[0-9+\-\s]+$/',
        ]);

        User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'peran' => 'siswa',
            'kelas_id' => $validated['kelas_id'],
            'nama_ortu' => $validated['nama_ortu'] ?? null,
            'no_wa_ortu' => $validated['no_wa_ortu'] ?? null,
        ]);

        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil ditambahkan!');
    }

    public function show(User $siswa)
    {
        if ($siswa->peran !== 'siswa') abort(404);
        
        $user = auth()->user();
        if (!$user->isAdmin()) {
            $allowedClassIds = $user->kelasWali()->pluck('id');
            if (!$allowedClassIds->contains($siswa->kelas_id)) {
                abort(403, 'Akses Ditolak.');
            }
        }

        $siswa->load('kelas');
        return view('siswa.show', compact('siswa'));
    }

    public function edit(User $siswa)
    {
        if ($siswa->peran !== 'siswa') abort(404);
        
        $user = auth()->user();
        $kelas = $user->isAdmin() ? Kelas::all() : $user->kelasWali()->get();
        
        $daftarPeran = [
            'siswa' => '👨‍🎓 Siswa',
            'guru' => '👨‍🏫 Guru',
            'wali_kelas' => '👨‍💼 Wali Kelas',
            'tamu' => '👤 Tamu',
        ];
        
        return view('siswa.edit', compact('siswa', 'kelas', 'daftarPeran'));
    }

    public function update(Request $request, User $siswa)
    {
        // Cek apakah data yang diedit adalah siswa
        if ($siswa->peran !== 'siswa') abort(404);
        
        $user = auth()->user();
        $allowedKelasIds = $user->isAdmin() ? Kelas::pluck('id') : $user->kelasWali()->pluck('id');

        // ⚠️ OPTIMASI: Tambahkan validasi password
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $siswa->id,
            'password' => 'nullable|string|min:6', // Validasi jika password diisi
            'kelas_id' => ['required', Rule::in($allowedKelasIds)],
            'peran' => 'required|in:siswa,guru,wali_kelas,tamu',
            'nama_ortu' => 'nullable|string|max:255',
            'no_wa_ortu' => 'nullable|string|max:20|regex:/^[0-9+\-\s]+$/',
        ]);

        // ⚠️ OPTIMASI: Kumpulkan semua data dalam satu array untuk update sekali saja
        $data = [
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'kelas_id' => $validated['kelas_id'],
            'peran' => $validated['peran'],
            'nama_ortu' => $validated['nama_ortu'] ?? null,
            'no_wa_ortu' => $validated['no_wa_ortu'] ?? null,
        ];

        // Update password hanya jika user mengisinya
        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        // Eksekusi update ke database (hanya 1 query)
        $siswa->update($data);

        return redirect()->route('siswa.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(User $siswa)
    {
        if ($siswa->peran !== 'siswa') abort(404);
        
        $user = auth()->user();
        if (!$user->isAdmin()) {
            $allowedClassIds = $user->kelasWali()->pluck('id');
            if (!$allowedClassIds->contains($siswa->kelas_id)) {
                abort(403, 'Akses Ditolak.');
            }
        }

        $siswa->delete();
        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil dihapus.');
    }

    // ==========================================
    // FITUR EXPORT & IMPORT
    // ==========================================

    public function export() 
    {
        return Excel::download(new SiswaExport, 'Data-Siswa-' . date('Y-m-d') . '.xlsx');
    }

    public function import(Request $request) 
    {
        $request->validate([
            'file_import' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new SiswaImport, $request->file('file_import'));
            return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil diimport!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }
            return redirect()->route('siswa.index')->withErrors(['import' => $errors]);
        } catch (\Exception $e) {
            return redirect()->route('siswa.index')->with('error', 'Gagal mengimport data. Pastikan format file sesuai.');
        }
    }

    public function deleteAll()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Akses Ditolak. Hanya Administrator.');
        }

        $totalSiswa = User::where('peran', 'siswa')->count();

        if ($totalSiswa === 0) {
            return redirect()->route('siswa.index')->with('error', 'Tidak ada data siswa untuk dihapus.');
        }

        User::where('peran', 'siswa')->delete();

        return redirect()->route('siswa.index')->with('success', "Berhasil menghapus {$totalSiswa} data siswa.");
    }
}
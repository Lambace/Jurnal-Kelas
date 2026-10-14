<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GuruExport;
use App\Imports\GuruImport;


class GuruController extends Controller
{
    public function __construct() {
        if (auth()->user()->peran !== 'admin') abort(403, 'Akses Ditolak');
    }

        public function index() 
    {
        // Tampilkan guru DAN wali_kelas (karena keduanya adalah pengajar)
        $guru = User::whereIn('peran', ['guru', 'wali_kelas'])
                    ->orderBy('nama')
                    ->paginate(10);
        return view('guru.index', compact('guru'));
    }

    public function create() { return view('guru.create'); }

        public function export() 
    {
        return Excel::download(new GuruExport, 'Data-Guru-' . date('Y-m-d') . '.xlsx');
    }

    public function import(Request $request) 
    {
        $request->validate([
            'file_import' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new GuruImport, $request->file('file_import'));
            return redirect()->route('guru.index')->with('success', 'Data guru berhasil diimport!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }
            return redirect()->route('guru.index')->withErrors(['import' => $errors]);
        } catch (\Exception $e) {
            return redirect()->route('guru.index')->with('error', 'Gagal mengimport data. Pastikan format file sesuai.');
        }
    }
public function store(Request $request)
{
    $validated = $request->validate([
        'nama' => 'required|string|max:255',
        // 'peran' => 'required|in:guru,wali_kelas',  // ← HAPUS atau KOMENTARI baris ini
        'nip' => 'nullable|string|max:50',
        'no_hp' => 'nullable|string|max:20',
    ]);

    // ✨ OTOMATIS set peran = 'guru' untuk halaman ini
    $validated['peran'] = 'guru';

    // Generate email otomatis
    $email = User::generateEmail($validated['nama'], 'guru');

    User::create([
        'nama' => $validated['nama'],
        'email' => $email,
        'password' => Hash::make(User::defaultPassword()),
        'peran' => $validated['peran'], // akan selalu 'guru'
        'nip' => $validated['nip'] ?? null,
        'no_hp' => $validated['no_hp'] ?? null,
    ]);

    return redirect()->route('guru.index')
        ->with('success', "Guru berhasil ditambahkan! Email: {$email} | Password: " . User::defaultPassword());
}

     public function edit(User $guru) 
    {
        // PERBAIKAN: Izinkan akses edit untuk 'guru' DAN 'wali_kelas'
        if (!in_array($guru->peran, ['guru', 'wali_kelas'])) {
            abort(404, 'Data tidak ditemukan atau tidak memiliki akses.');
        }
        
        return view('guru.edit', compact('guru'));
    }

public function update(Request $request, User $guru) 
    {
        // PERBAIKAN: Izinkan update untuk 'guru' DAN 'wali_kelas'
        if (!in_array($guru->peran, ['guru', 'wali_kelas'])) {
            abort(403, 'Akses Ditolak.');
        }

        $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|unique:users,nip,' . $guru->id,
            'email' => 'required|email|unique:users,email,' . $guru->id,
            'no_hp' => 'nullable|string',
            'peran' => 'required|in:guru,wali_kelas,admin',
        ]);

        $data = $request->only(['nama', 'nip', 'email', 'no_hp', 'peran']);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $guru->update($data);
        
        return redirect()->route('guru.index')->with('success', 'Data Guru berhasil diperbarui!');
    }


    public function destroy(User $guru) 
    {
        // PERBAIKAN: Izinkan hapus untuk 'guru' DAN 'wali_kelas'
        if (!in_array($guru->peran, ['guru', 'wali_kelas'])) {
            abort(403, 'Akses Ditolak.');
        }
        
        $guru->delete();
        return redirect()->route('guru.index')->with('success', 'Data berhasil dihapus.');
    }
}
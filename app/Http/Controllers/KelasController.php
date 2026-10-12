<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function __construct() {
        if (auth()->user()->peran !== 'admin') abort(403, 'Akses Ditolak');
    }

    public function index() {
        $kelas = Kelas::with('waliKelas')->orderBy('nama')->paginate(10);
        return view('kelas.index', compact('kelas'));
    }

     public function create() 
        {
            // PERBAIKAN: Ambil guru DAN wali_kelas agar keduanya muncul di dropdown
            $daftarGuru = User::whereIn('peran', ['guru', 'wali_kelas'])
                            ->orderBy('nama')
                            ->get();
            
            return view('kelas.create', compact('daftarGuru'));
        }

    public function store(Request $request) {
        $request->validate([
            'nama' => 'required|string|max:255',
            'tingkat' => 'required|string',
            'jurusan' => 'required|string',
            'wali_kelas_id' => 'nullable|exists:users,id',
        ]);

        Kelas::create($request->all());
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dibuat!');
    }

        public function edit(Kelas $kelas) 
        {
            // PERBAIKAN: Sama seperti create(), ambil guru DAN wali_kelas
            $daftarGuru = User::whereIn('peran', ['guru', 'wali_kelas'])
                            ->orderBy('nama')
                            ->get();
            
            return view('kelas.edit', compact('kelas', 'daftarGuru'));
        }
        
    public function update(Request $request, Kelas $kelas) {
        $request->validate([
            'nama' => 'required|string|max:255',
            'tingkat' => 'required|string',
            'jurusan' => 'required|string',
            'wali_kelas_id' => 'nullable|exists:users,id',
        ]);

        $kelas->update($request->all());
        return redirect()->route('kelas.index')->with('success', 'Kelas diperbarui!');
    }

    public function destroy(Kelas $kelas) {
        $kelas->delete();
        return redirect()->route('kelas.index')->with('success', 'Kelas dihapus.');
    }
}
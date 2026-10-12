<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller

{



    public function index(Request $request)
    {
        $query = User::query();

        // Filter berdasarkan role
        if ($request->filled('role')) {
            $query->where('peran', $request->role);
        }

        // Filter berdasarkan kelas (untuk siswa)
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Search berdasarkan nama atau email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->with('kelas')
            ->orderBy('peran')
            ->orderBy('nama')
            ->paginate(15);

        $daftarKelas = Kelas::orderBy('nama')->get();
        $daftarRole = [
            'admin' => '️ Admin',
            'guru' => '‍🏫 Guru',
            'wali_kelas' => '💼 Wali Kelas',
            'siswa' => '👨‍ Siswa',
        ];

        return view('users.index', compact('users', 'daftarKelas', 'daftarRole'));
    }

        public function create()
    {
        $kelas = \App\Models\Kelas::orderBy('nama')->get();
        
        $daftarRole = [
            'admin' => '⚙️ Admin',
            'guru' => '👨‍🏫 Guru',
            'wali_kelas' => '👨‍💼 Wali Kelas',
            'siswa' => '👨‍🎓 Siswa',
            'tamu' => '👤 Tamu (Belum Aktivasi)',
        ];

        return view('users.create', compact('kelas', 'daftarRole'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'peran' => 'required|in:admin,guru,wali_kelas,siswa,tamu',
            'kelas_id' => 'nullable|exists:kelas,id',
            'nip' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:20',
        ]);

        // Hash password sebelum disimpan
        $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);

        \App\Models\User::create($validated);

        return redirect()->route('users.index')->with('success', 'User baru berhasil ditambahkan!');
    }

    public function edit(User $user)
    {
        $kelas = Kelas::orderBy('nama')->get();
        
        $daftarRole = [
            'admin' => '⚙️ Admin',
            'guru' => '‍🏫 Guru',
            'wali_kelas' => '👨💼 Wali Kelas',
            'siswa' => '‍ Siswa',
        ];

        return view('users.edit', compact('user', 'kelas', 'daftarRole'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'peran' => 'required|in:admin,guru,wali_kelas,siswa',
            'kelas_id' => 'nullable|exists:kelas,id',
            'nip' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:20',
            'password' => 'nullable|min:6',
        ]);

        $data = [
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'peran' => $validated['peran'],
            'kelas_id' => $validated['kelas_id'] ?? null,
            'nip' => $validated['nip'] ?? null,
            'no_hp' => $validated['no_hp'] ?? null,
        ];

        // Update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Data user berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        // Jangan izinkan menghapus admin utama atau diri sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
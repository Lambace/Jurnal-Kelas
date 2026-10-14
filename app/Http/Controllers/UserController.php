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
        $user = auth()->user();

        // 🛡️ PENGAMAN: Hanya admin yang boleh akses manajemen user
        if (!$user->isAdmin()) {
            abort(403, 'Akses Ditolak.');
        }

        // Ambil parameter search jika ada
        $search = $request->get('search');

        // ⚠️ PERBAIKAN: Hanya ambil peran selain 'siswa' agar tidak menumpuk
        $allowedRoles = ['admin', 'guru', 'wali_kelas', 'tamu'];
        
        $query = User::whereIn('peran', $allowedRoles);

        // Fitur pencarian (opsional, jika ada di view Anda)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        // Urutkan berdasarkan nama
        $users = $query->orderBy('nama')->paginate(15);

        return view('users.index', compact('users', 'search'));
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
            'peran' => 'required|in:admin,guru,wali_kelas,siswa,tamu',
            'kelas_id' => 'nullable|exists:kelas,id',
            'nip' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:20',
            'no_wa_ortu' => 'nullable|string|max:20',
        ]);

        // ✨ Email & password otomatis sesuai peran
        $email = User::generateEmail($validated['nama'], $validated['peran']);

        User::create([
            'nama' => $validated['nama'],
            'email' => $email,
            'password' => Hash::make(User::defaultPassword()),
            'peran' => $validated['peran'],
            'kelas_id' => $validated['kelas_id'] ?? null,
            'nip' => $validated['nip'] ?? null,
            'no_hp' => $validated['no_hp'] ?? null,
            'no_wa_ortu' => $validated['no_wa_ortu'] ?? null,
        ]);

        return redirect()->route('users.index')
            ->with('success', "User berhasil dibuat! Email: {$email} | Password: " . User::defaultPassword());
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
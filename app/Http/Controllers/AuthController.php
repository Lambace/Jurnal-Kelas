<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) return redirect()->route('dashboard');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function showRegister() { return view('auth.register'); }

     public function register(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed', // confirmed artinya harus ada password_confirmation
        ]);

        // 2. Buat user baru dengan peran TAMU secara eksplisit
        $user = \App\Models\User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'peran' => 'tamu', // ⚠️ PASTIKAN BARIS INI ADA DAN BERNILAI 'tamu'
        ]);

        // 3. Login otomatis setelah mendaftar
        \Illuminate\Support\Facades\Auth::login($user);

        // 4. Arahkan ke halaman tamu, bukan dashboard
        return redirect()->route('tamu.index')->with('success', 'Pendaftaran berhasil! Akun Anda sedang dalam proses aktivasi oleh Admin.');
    }
    public function redirectToProvider($provider)
    {
        try {
            return Socialite::driver($provider)->redirect();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Provider tidak tersedia.');
        }
    }

    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Gagal login dengan ' . ucfirst($provider));
        }

        $user = User::where('provider_id', $socialUser->getId())
                    ->orWhere('email', $socialUser->getEmail())
                    ->first();

        if ($user) {
            $user->update(['provider' => $provider, 'provider_id' => $socialUser->getId()]);
        } else {
            $user = User::create([
                'nama' => $socialUser->getName() ?? 'User ' . $provider,
                'email' => $socialUser->getEmail(),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'peran' => 'siswa',
                'password' => Hash::make(str()->random(16)),
            ]);
        }

        Auth::login($user);
        request()->session()->regenerate();
        return redirect()->route('dashboard');
    }
}
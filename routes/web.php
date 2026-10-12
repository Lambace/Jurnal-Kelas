<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JurnalController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\UserController;   
use App\Http\Controllers\TamuController;   
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LaporanController;

// ==========================================
// 1. PUBLIC ROUTES (Tidak butuh login)
// ==========================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Social Login (Opsional, bisa di-comment jika tidak dipakai)
Route::get('/auth/{provider}/redirect', [AuthController::class, 'redirectToProvider'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback'])->name('social.callback');

// ==========================================
// 2. PROTECTED ROUTES (Wajib login)
// ==========================================
Route::middleware('auth')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard & Tamu
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/attendance-data', [DashboardController::class, 'getAttendanceData'])->name('dashboard.attendanceData');
    
    Route::get('/tamu', [TamuController::class, 'index'])->name('tamu.index');
    Route::post('/tamu/attendance-data', [TamuController::class, 'attendanceData'])->name('tamu.attendanceData');

    // Manajemen User (Hanya untuk Admin)
    Route::get('/manajemen-user', [UserController::class, 'index'])->name('users.index');
    Route::get('/manajemen-user/create', [UserController::class, 'create'])->name('users.create'); // ⚠️
    Route::post('/manajemen-user', [UserController::class, 'store'])->name('users.store');         // 
    Route::get('/manajemen-user/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/manajemen-user/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/manajemen-user/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // ------------------------------------------
    // JURNAL HARIAN
    // ------------------------------------------
    // Route spesifik (tanpa parameter)
    Route::get('/jurnal', [JurnalController::class, 'index'])->name('jurnal.index');
    Route::get('/jurnal/create', [JurnalController::class, 'create'])->name('jurnal.create');
    Route::post('/jurnal', [JurnalController::class, 'store'])->name('jurnal.store');
    Route::post('/jurnal/{jurnal}/verifikasi', [JurnalController::class, 'verifikasi'])->name('jurnal.verifikasi');
    
    // Route dengan parameter
    Route::get('/jurnal/{jurnal}', [JurnalController::class, 'show'])->name('jurnal.show');
    Route::get('/jurnal/{jurnal}/edit', [JurnalController::class, 'edit'])->name('jurnal.edit');
    Route::put('/jurnal/{jurnal}', [JurnalController::class, 'update'])->name('jurnal.update');
    Route::delete('/jurnal/{jurnal}', [JurnalController::class, 'destroy'])->name('jurnal.destroy');

    // ------------------------------------------
    // DATA SISWA
    // ------------------------------------------
    // A. Route SPESIFIK (Tanpa parameter) - HARUS DI ATAS
    Route::get('/siswa/export', [SiswaController::class, 'export'])->name('siswa.export');
    Route::post('/siswa/import', [SiswaController::class, 'import'])->name('siswa.import');
    Route::get('/siswa/create', [SiswaController::class, 'create'])->name('siswa.create');
    Route::post('/siswa', [SiswaController::class, 'store'])->name('siswa.store');
    Route::delete('/siswa/delete-all', [SiswaController::class, 'deleteAll'])->name('siswa.deleteAll');
    
    // B. Route dengan PARAMETER ({siswa}) - HARUS DI BAWAH
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
    Route::get('/siswa/{siswa}', [SiswaController::class, 'show'])->name('siswa.show');
    Route::get('/siswa/{siswa}/edit', [SiswaController::class, 'edit'])->name('siswa.edit');
    Route::put('/siswa/{siswa}', [SiswaController::class, 'update'])->name('siswa.update');
    Route::delete('/siswa/{siswa}', [SiswaController::class, 'destroy'])->name('siswa.destroy');

    // ------------------------------------------
    // DATA GURU
    // ------------------------------------------
    // A. Route SPESIFIK (Tanpa parameter) - HARUS DI ATAS
    Route::get('/guru/export', [GuruController::class, 'export'])->name('guru.export');
    Route::post('/guru/import', [GuruController::class, 'import'])->name('guru.import');
    Route::get('/guru/create', [GuruController::class, 'create'])->name('guru.create');
    Route::post('/guru', [GuruController::class, 'store'])->name('guru.store');
    
    // B. Route dengan PARAMETER ({guru}) - HARUS DI BAWAH
    Route::get('/guru', [GuruController::class, 'index'])->name('guru.index');
    Route::get('/guru/{guru}/edit', [GuruController::class, 'edit'])->name('guru.edit');
    Route::put('/guru/{guru}', [GuruController::class, 'update'])->name('guru.update');
    Route::delete('/guru/{guru}', [GuruController::class, 'destroy'])->name('guru.destroy');

    // ------------------------------------------
    // MANAJEMEN KELAS
    // ------------------------------------------
    // A. Route SPESIFIK (Tanpa parameter) - HARUS DI ATAS
    Route::get('/kelas/create', [KelasController::class, 'create'])->name('kelas.create');
    Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
    
    // B. Route dengan PARAMETER ({kelas}) - HARUS DI BAWAH
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::get('/kelas/{kelas}/edit', [KelasController::class, 'edit'])->name('kelas.edit');
    Route::put('/kelas/{kelas}', [KelasController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/{kelas}', [KelasController::class, 'destroy'])->name('kelas.destroy');

    // Laporan (Khusus Wali Kelas & Admin)
    Route::get('/laporan-kehadiran', [LaporanController::class, 'kehadiran'])->name('laporan.kehadiran');

});

// ==========================================
// 3. ROOT REDIRECT
// ==========================================
Route::get('/', function () {
    return redirect()->route('login');
});
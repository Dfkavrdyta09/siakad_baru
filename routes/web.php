<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\KrsController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\PenggunaController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.attempt');
});

// Halaman internal: hanya bisa dibuka bila SUDAH login
Route::middleware(['auth', 'no-back'])->group(function () {
    // Halaman awal diarahkan ke dashboard (dari Jobsheet 08)
    Route::get('/', fn () => redirect()->route('dashboard'));

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/mahasiswa', [MahasiswaController::class, 'index'])
        ->name('mahasiswa.index');
    Route::get('/mahasiswa/{nim}', [MahasiswaController::class, 'show'])
        ->name('mahasiswa.show');
    Route::get('/mata-kuliah', [MataKuliahController::class, 'index'])
        ->name('matakuliah.index');
    Route::get('/krs', [KrsController::class, 'index'])
        ->name('krs.index');

    // Dari Jobsheet 07: menampilkan versi PHP/MySQL dan nama database, jadi dilindungi
    Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');

    // Data Dosen (Jobsheet 11)

    // 1) Endpoint JSON DataTables: HARUS di atas route yang memuat {dosen}
    Route::get('/dosen/data', [DosenController::class, 'data'])->name('dosen.data');

    // 2) Tambah, ubah, hapus: khusus admin (role berjalan SETELAH auth)
    Route::middleware('role:admin')->group(function () {
        Route::resource('dosen', DosenController::class)
            ->except(['index', 'show']);
    });

    // 3) Daftar dan detail: semua pengguna yang sudah login
    Route::resource('dosen', DosenController::class)
        ->only(['index', 'show']);

    // Hanya admin: middleware role berjalan SETELAH auth karena berada di dalam grup auth
    Route::get('/pengguna', [PenggunaController::class, 'index'])
        ->middleware('role:admin')
        ->name('pengguna.index');
});

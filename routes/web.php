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

Route::middleware(['auth', 'no-back'])->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // ===== Mahasiswa =====
    Route::get('/mahasiswa/data', [MahasiswaController::class, 'data'])
        ->name('mahasiswa.data');
    Route::middleware('role:admin')->group(function () {
        Route::resource('mahasiswa', MahasiswaController::class)
            ->except(['index', 'show']);
    });
    Route::resource('mahasiswa', MahasiswaController::class)
        ->only(['index', 'show']);

    // ===== Mata Kuliah =====
    Route::get('/mata-kuliah/data', [MataKuliahController::class, 'data'])
        ->name('matakuliah.data');
    Route::middleware('role:admin')->group(function () {
        Route::resource('mata-kuliah', MataKuliahController::class)
            ->names('matakuliah')
            ->parameters(['mata-kuliah' => 'mataKuliah'])
            ->except(['index', 'show']);
    });
    Route::resource('mata-kuliah', MataKuliahController::class)
        ->names('matakuliah')
        ->parameters(['mata-kuliah' => 'mataKuliah'])
        ->only(['index', 'show']);

    // ===== KRS =====
    Route::get('/krs/data', [KrsController::class, 'data'])
        ->name('krs.data');
    Route::middleware('role:admin')->group(function () {
        Route::resource('krs', KrsController::class)
            ->parameters(['krs' => 'krs']);
        Route::post('/krs/{krs}/detail', [KrsController::class, 'storeDetail'])
            ->name('krs.detail.store');
        Route::delete('/krs/{krs}/detail/{detail}', [KrsController::class, 'destroyDetail'])
            ->name('krs.detail.destroy');
        Route::patch('/krs/{krs}/status', [KrsController::class, 'updateStatus'])
            ->name('krs.status');
    });

    Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');

    // ===== Data Dosen (Jobsheet 11) =====
    Route::get('/dosen/data', [DosenController::class, 'data'])
        ->name('dosen.data');
    Route::middleware('role:admin')->group(function () {
        Route::resource('dosen', DosenController::class)
            ->except(['index', 'show']);
    });
    Route::resource('dosen', DosenController::class)->only(['index', 'show']);

    Route::get('/pengguna', [PenggunaController::class, 'index'])
        ->middleware('role:admin')
        ->name('pengguna.index');
});

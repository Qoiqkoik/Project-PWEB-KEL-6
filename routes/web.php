<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KamarController;
use App\Http\Controllers\PenghuniController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PengaduanController;

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('penghuni.dashboard');
    }

    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ================= ROLE PENGHUNI =================
Route::middleware(['auth', 'role:penghuni'])->prefix('penghuni')->name('penghuni.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'penghuni'])->name('dashboard');

    // Modul 3: Tagihan & Upload Bukti
    Route::get('/pembayaran', [PembayaranController::class, 'indexPenghuni'])->name('pembayaran.index');
    Route::post('/pembayaran/{pembayaran}/upload', [PembayaranController::class, 'uploadBukti'])->name('pembayaran.upload');

    // Modul 4: Pengaduan
    Route::get('/pengaduan', [PengaduanController::class, 'indexPenghuni'])->name('pengaduan.index');
    Route::get('/pengaduan/buat', [PengaduanController::class, 'create'])->name('pengaduan.create');
    Route::post('/pengaduan', [PengaduanController::class, 'store'])->name('pengaduan.store');
});

// ================= ROLE ADMIN =================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

    // Modul 1: Penghuni
    Route::resource('penghuni', PenghuniController::class)->except(['show']);

    // Modul 2: Kamar
    Route::resource('kamar', KamarController::class)->except(['show']);

    // Modul 3: Pembayaran & Laporan Keuangan
    Route::get('/pembayaran', [PembayaranController::class, 'indexAdmin'])->name('pembayaran.index');
    Route::get('/pembayaran/buat', [PembayaranController::class, 'create'])->name('pembayaran.create');
    Route::post('/pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');
    Route::put('/pembayaran/{pembayaran}/konfirmasi', [PembayaranController::class, 'konfirmasi'])->name('pembayaran.konfirmasi');
    Route::get('/laporan-keuangan', [PembayaranController::class, 'laporan'])->name('pembayaran.laporan');

    // Modul 4: Pengaduan & Laporan Pengaduan
    Route::get('/pengaduan', [PengaduanController::class, 'indexAdmin'])->name('pengaduan.index');
    Route::put('/pengaduan/{pengaduan}', [PengaduanController::class, 'updateStatus'])->name('pengaduan.update');
    Route::get('/laporan-pengaduan', [PengaduanController::class, 'laporan'])->name('pengaduan.laporan');
});

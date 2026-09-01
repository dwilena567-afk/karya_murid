<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\BerandaController; 
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\VerifikasiController;
use App\Http\Controllers\KaryamuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\TransaksiController as AdminTransaksiController;

Route::get('/a', function () {
    return view('welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// 1. PERBAIKAN: Arahkan ke BerandaController agar data $karyasTerbaru muncul di halaman utama
Route::get('/', [BerandaController::class, 'index'])->name('beranda.index');

// Rute Katalog
Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog.index');
Route::get('/katalog/{id}', [KatalogController::class, 'show'])->name('katalog.show');

// Rute Webhook Midtrans
Route::post('/midtrans/callback', [TransaksiController::class, 'callback'])->name('midtrans.callback');

// Area Terotentikasi
Route::middleware(['auth'])->group(function () {
    
    // Rute Keranjang
    Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
    Route::post('/keranjang', [KeranjangController::class, 'store'])->name('keranjang.store');
    Route::delete('/keranjang/{id}', [KeranjangController::class, 'destroy'])->name('keranjang.destroy'); 

    // Rute Transaksi
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index'); // TAMBAHAN: Riwayat Transaksi
    Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
    Route::get('/transaksi/{id}', [TransaksiController::class, 'show'])->name('transaksi.show');
    
    // Rute Kelola Karya (Karyamu)
    Route::resource('karyamu', KaryamuController::class)->except(['show']);
    
   

    // Rute Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
});

// Rute Verifikasi Karya (Hanya bisa diakses oleh Admin/Guru)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/verifikasi', [VerifikasiController::class, 'index'])->name('verifikasi.index');
    Route::patch('/verifikasi/{id}/approve', [VerifikasiController::class, 'approve'])->name('verifikasi.approve');
    Route::patch('/verifikasi/{id}/reject', [VerifikasiController::class, 'reject'])->name('verifikasi.reject');
});

require __DIR__.'/auth.php';
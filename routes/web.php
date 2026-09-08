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



// Beranda menjadi halaman awal dan memuat karya terbaru yang sudah disetujui.
Route::get('/', [BerandaController::class, 'index'])->name('beranda.index');

// Rute Katalog
Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog.index');
Route::get('/katalog/{id}', [KatalogController::class, 'show'])->name('katalog.show');

// Dipanggil Midtrans; pengecualian CSRF diatur pada bootstrap/app.php.
Route::post('/midtrans/callback', [TransaksiController::class, 'callback'])->name('midtrans.callback');

// Area Terotentikasi
Route::middleware(['auth'])->group(function () {
    
    // Keranjang dibatasi auth karena setiap item terhubung ke user_id.
    Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
    Route::post('/keranjang', [KeranjangController::class, 'store'])->name('keranjang.store');
    Route::delete('/keranjang/{id}', [KeranjangController::class, 'destroy'])->name('keranjang.destroy'); 

    // Checkout, detail, dan konfirmasi status pembayaran dari browser.
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
    Route::get('/transaksi/{id}', [TransaksiController::class, 'show'])->name('transaksi.show');
    Route::post('/transaksi/{id}/confirm-payment', [TransaksiController::class, 'confirmPayment'])->name('transaksi.confirm-payment');
    
    // Pengguna hanya mengelola karya miliknya melalui resource controller.
    Route::resource('karyamu', KaryamuController::class)->except(['show']);
    
   

    // Profil dan kredensial hanya boleh diubah oleh pemilik akun.
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
});

// Verifikasi memakai auth dan admin middleware untuk membatasi keputusan moderasi.
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/verifikasi', [VerifikasiController::class, 'index'])->name('verifikasi.index');
    Route::patch('/verifikasi/{id}/approve', [VerifikasiController::class, 'approve'])->name('verifikasi.approve');
    Route::patch('/verifikasi/{id}/reject', [VerifikasiController::class, 'reject'])->name('verifikasi.reject');
});

require __DIR__.'/auth.php';
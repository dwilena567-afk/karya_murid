<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\KatalogController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\VerifikasiController;

Route::get('/', function () {
    return view('welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Rute Katalog
Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog.index');
Route::get('/katalog/{id}', [KatalogController::class, 'show'])->name('katalog.show');

// Rute Keranjang dan Checkout (Untuk User yang sudah login)
Route::middleware(['auth'])->group(function () {
    Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
    Route::post('/keranjang', [KeranjangController::class, 'store'])->name('keranjang.store');

    Route::post('/checkout', [TransaksiController::class, 'store'])->name('checkout.store');
});

// Rute Verifikasi Karya (Untuk Admin/Guru)
Route::get('/verifikasi', [VerifikasiController::class, 'index'])->name('verifikasi.index');
Route::patch('/verifikasi/{id}/approve', [VerifikasiController::class, 'approve'])->name('verifikasi.approve');
Route::patch('/verifikasi/{id}/reject', [VerifikasiController::class, 'reject'])->name('verifikasi.reject');




Route::get('/dashboard', function () {
        return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

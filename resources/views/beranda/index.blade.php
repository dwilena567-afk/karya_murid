@extends('layouts.main')

@section('content')
<style>
    /* Styling khusus untuk Hero Banner di Beranda */
    .hero-banner {
        background: linear-gradient(135deg, var(--steel-azure) 0%, var(--steel-blue) 100%);
        border-radius: 12px;
        color: white;
        box-shadow: 0 10px 20px rgba(5, 74, 145, 0.15);
    }
    .feature-card {
        background-color: white;
        border: 1px solid var(--wisteria-blue);
        border-radius: 10px;
        transition: transform 0.3s ease;
    }
    .feature-card:hover {
        transform: translateY(-5px);
        border-color: var(--steel-blue);
    }
</style>

<!-- Hero Section -->
<div class="hero-banner p-5 mb-4 text-center">
    <div class="container-fluid py-4">
       
        <h1 class="display-5 fw-bold mb-3">Selamat Datang di Galeri Karya Siswa</h1>
        <p class="col-md-10 mx-auto fs-6 mb-4" style="line-height: 1.6; opacity: 0.9;">
            Wadah inspiratif bagi siswa untuk memamerkan, mengapresiasi, dan mendukung kreativitas tanpa batas. 
            Jelajahi berbagai karya seni visual, patung, hingga seni digital hasil karya talenta-talenta muda berbakat.
        </p>
        <div class="d-flex justify-content-center gap-3 mt-4">
            <!-- Tombol Aksen (Harvest Orange) -->
            <a href="{{ route('katalog.index') }}" class="btn bg-accent btn-lg px-4 fw-bold shadow-sm" style="font-size: 0.95rem;">
                Jelajahi Katalog <i class="bi bi-arrow-right ms-1"></i>
            </a>
            
            <!-- Tombol sekunder (Hanya muncul jika belum login) -->
            @guest
            <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-4 fw-bold" style="font-size: 0.95rem;">
                Bergabung Sekarang
            </a>
            @endguest
        </div>
    </div>
</div>

<!-- Keunggulan Platform -->
<div class="row g-4 mt-2">
    <div class="col-md-4">
        <div class="feature-card h-100 p-4 text-center shadow-sm">
            <div class="d-inline-flex align-items-center justify-content-center bg-primary-custom text-white rounded-circle mb-3" style="width: 60px; height: 60px;">
                <i class="bi bi-palette fs-3"></i>
            </div>
            <h5 class="fw-bold text-primary-custom">Karya Autentik</h5>
            <p class="text-muted small mt-2">Semua karya adalah hasil kreativitas murni dari para siswa yang telah diverifikasi kualitasnya.</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="feature-card h-100 p-4 text-center shadow-sm">
            <div class="d-inline-flex align-items-center justify-content-center bg-primary-custom text-white rounded-circle mb-3" style="width: 60px; height: 60px;">
                <i class="bi bi-cart-check fs-3"></i>
            </div>
            <h5 class="fw-bold text-primary-custom">Transaksi Aman</h5>
            <p class="text-muted small mt-2">Sistem keranjang belanja yang terintegrasi memastikan setiap dukunganmu sampai ke kreator dengan aman.</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="feature-card h-100 p-4 text-center shadow-sm">
            <div class="d-inline-flex align-items-center justify-content-center bg-primary-custom text-white rounded-circle mb-3" style="width: 60px; height: 60px;">
                <i class="bi bi-mortarboard fs-3"></i>
            </div>
            <h5 class="fw-bold text-primary-custom">Dukung Siswa</h5>
            <p class="text-muted small mt-2">Setiap apresiasi dari Anda menjadi motivasi besar bagi siswa untuk terus berkarya dan berkembang.</p>
        </div>
    </div>
</div>
@endsection
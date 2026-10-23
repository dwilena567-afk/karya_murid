@extends('layouts.main')

@section('content')
<style>
    /* Styling khusus untuk Hero Banner di Beranda */
    .hero-banner {
        background:
            linear-gradient(rgba(5, 74, 145, 0.62), rgba(5, 74, 145, 0.72)),
            url('{{ asset('images/pemesinan11.jpg') }}') center 65% / cover no-repeat;
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
       
        <h1 class="display-5 fw-bold mb-3">Selamat Datang di Galeri SISKAR</h1>
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

<!-- Karya Terbaru -->
<section class="mb-5" aria-labelledby="latest-works-title">
    @if($karyasTerbaru->isNotEmpty())
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div class="text-center w-100">
            <h2 class="h4 fw-bold text-primary-custom mb-1" id="latest-works-title">Karya Terbaru</h2>
            <p class="text-muted small mb-0">Jelajahi karya terbaru dari para siswa.</p>
        </div>
    </div>
    @endif

    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
        @forelse($karyasTerbaru as $karya)
            <div class="col">
                <a href="{{ route('katalog.show', $karya->id) }}" class="product-card h-100 d-flex flex-column text-decoration-none text-dark">
                    <div class="position-relative">
                        <img src="{{ $karya->gambar ? asset('storage/' . $karya->gambar) : 'https://via.placeholder.com/450x450?text=Tidak+ada+gambar' }}"
                            class="w-100 object-fit-cover" style="height: 200px;" alt="{{ $karya->judul }}">
                        <span class="badge badge-category position-absolute top-0 end-0 m-2 px-2 py-1 shadow-sm">
                            {{ ucfirst($karya->kategori?->nama ?? 'Umum') }}
                        </span>
                    </div>
                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <h3 class="h6 fw-bold text-primary-custom mb-1 title-clamp">{{ $karya->judul }}</h3>
                        <small class="text-muted mb-3">
                            <i class="bi bi-person-fill me-1" style="color: var(--steel-blue);"></i>
                            {{ $karya->pembuat?->name ?? 'Siswa' }}
                        </small>
                        <div class="mt-auto pt-2 border-top border-custom d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-accent">
                                {{ $karya->harga == 0 ? 'Gratis' : 'Rp' . number_format($karya->harga, 0, ',', '.') }}
                            </span>
                            @if($karya->stok > 0)
                                <small class="text-muted">Stok: {{ $karya->stok }}</small>
                            @else
                                <small class="text-danger">Stok Habis</small>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="text-center w-100">
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2" aria-hidden="true"></i>
                    Karya terbaru akan tampil di sini setelah tersedia.
                </div>
            </div>
        @endforelse
    </div>
</section>

<!-- Keunggulan Platform -->
<div class="row g-4 mt-2">
    <div class="col-md-4">
        <div class="feature-card h-100 p-4 text-center shadow-sm">
            <div class="d-inline-flex align-items-center justify-content-center bg-primary-custom text-white rounded-circle mb-3" style="width: 60px; height: 60px;">
                <i class="bi bi-palette fs-3"></i>
            </div>
            <h5 class="fw-bold text-primary-custom">Karya Autentik</h5>
            <p class="text-muted small mt-2">Semua karya adalah hasil kreativitas murni dari para siswa yang telah diverifikasi kualitasnya oleh tim kami.</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="feature-card h-100 p-4 text-center shadow-sm">
            <div class="d-inline-flex align-items-center justify-content-center bg-primary-custom text-white rounded-circle mb-3" style="width: 60px; height: 60px;">
                <i class="bi bi-cart-check fs-3"></i>
            </div>
            <h5 class="fw-bold text-primary-custom">Pembayaran Terintegrasi</h5>
            <p class="text-muted small mt-2">Pembayaran pesanan berbayar diproses melalui Midtrans Snap. Karya gratis dapat di-checkout tanpa pembayaran. Pilihan yang dapat tersedia antara lain transfer bank atau virtual account, e-wallet, QRIS, dan kartu kredit.
            </p>
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

<!-- Bantuan dan Kontak -->
<section class="mt-5" aria-labelledby="help-title">
    <div class="text-center mb-4">
        <h2 class="h4 fw-bold text-primary-custom mb-1" id="help-title">Bantuan & Kontak</h2>
        <p class="text-muted small mb-0">Temukan jawaban atau hubungi kami jika perlu bantuan lebih lanjut.</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="feature-card h-100 p-4 shadow-sm">
                <h3 class="h5 fw-bold text-primary-custom mb-3">Pertanyaan Umum</h3>
                <div class="accordion" id="help-faq">
                    <div class="accordion-item">
                        <h4 class="accordion-header" id="faq-heading-buy">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq-buy" aria-expanded="false" aria-controls="faq-buy">
                                Bagaimana cara membeli karya?
                            </button>
                        </h4>
                        <div id="faq-buy" class="accordion-collapse collapse" aria-labelledby="faq-heading-buy" data-bs-parent="#help-faq">
                            <div class="accordion-body text-muted">
                                Masuk atau daftar akun, pilih karya dari katalog, lalu masukkan ke keranjang dan lanjutkan checkout.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h4 class="accordion-header" id="faq-heading-payment">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq-payment" aria-expanded="false" aria-controls="faq-payment">
                                Metode pembayaran apa yang tersedia?
                            </button>
                        </h4>
                        <div id="faq-payment" class="accordion-collapse collapse" aria-labelledby="faq-heading-payment" data-bs-parent="#help-faq">
                            <div class="accordion-body text-muted">
                                Pembayaran diproses melalui Midtrans Snap. Pilihan yang tersedia akan ditampilkan saat checkout dan dapat mencakup transfer bank atau virtual account, e-wallet, QRIS, serta kartu kredit. Karya gratis dapat di-checkout tanpa pembayaran.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h4 class="accordion-header" id="faq-heading-status">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq-status" aria-expanded="false" aria-controls="faq-status">
                                Di mana saya bisa melihat status pesanan?
                            </button>
                        </h4>
                        <div id="faq-status" class="accordion-collapse collapse" aria-labelledby="faq-heading-status" data-bs-parent="#help-faq">
                            <div class="accordion-body text-muted">
                                Buka menu Transaksi setelah masuk, lalu pilih Detail pada pesanan untuk melihat status dan informasi pembayaran.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h4 class="accordion-header" id="faq-heading-upload">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq-upload" aria-expanded="false" aria-controls="faq-upload">
                                Bagaimana karya saya bisa tampil di katalog?
                            </button>
                        </h4>
                        <div id="faq-upload" class="accordion-collapse collapse" aria-labelledby="faq-heading-upload" data-bs-parent="#help-faq">
                            <div class="accordion-body text-muted">
                                Masuk menggunakan akun murid, tambahkan karya melalui menu pengelolaan karya, lalu tunggu proses verifikasi. Karya yang disetujui akan tampil di katalog.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="feature-card h-100 p-4 shadow-sm">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary-custom text-white rounded-circle mb-3"
                    style="width: 52px; height: 52px;">
                    <i class="bi bi-headset fs-4" aria-hidden="true"></i>
                </div>
                <h3 class="h5 fw-bold text-primary-custom">Masih butuh bantuan?</h3>
                <p class="text-muted small">Hubungi tim kami melalui email atau WhatsApp. Sertakan nomor pesanan jika pertanyaan Anda terkait transaksi.</p>
                <div class="d-flex flex-column gap-2">
                    <a href="mailto:mail.smkpgri3malang@gmail.com" class="btn bg-primary-custom text-white fw-bold">
                        <i class="bi bi-envelope me-2" aria-hidden="true"></i>Email kami
                    </a>
                    <a href="https://wa.me/6282133000370" class="btn bg-primary-custom text-white fw-bold" target="_blank" rel="noopener noreferrer">
                        <i class="bi bi-whatsapp me-2" aria-hidden="true"></i>Hubungi via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
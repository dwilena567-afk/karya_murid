@extends('layouts.main')

@section('content')
    <style>
        /* Efek hover khusus untuk kartu produk */
        .product-card {
            background: white;
            border: 1px solid var(--wisteria-blue);
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(62, 124, 177, 0.2);
            /* Bayangan menggunakan Steel Blue */
            border-color: var(--steel-azure);
        }

        .badge-category {
            background-color: var(--wisteria-blue);
            color: white;
            font-weight: 500;
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary-custom m-0">Eksplorasi Karya</h4>
    </div>

    <!-- Grid Bootstrap -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        @forelse($karyas as $karya)
            <div class="col">
                <div class="product-card h-100 d-flex flex-column">
                    <a href="{{ route('katalog.show', $karya->id) }}"
                        class="text-decoration-none text-dark flex-grow-1 d-flex flex-column">

                        <!-- Gambar Produk -->
                        <div class="position-relative">
                            <img src="{{ $karya->gambar ? asset('storage/' . $karya->gambar) : 'https://via.placeholder.com/400x300' }}"
                                class="w-100 object-fit-cover" style="height: 200px;" alt="{{ $karya->judul }}">
                            <!-- Lencana Kategori Mengambang -->
                            <span class="badge badge-category position-absolute top-0 end-0 m-2 px-2 py-1">
                                {{ ucfirst($karya->kategori?->nama ?? 'Umum') }}
                            </span>
                        </div>

                        <!-- Badan Kartu -->
                        <div class="p-3 d-flex flex-column flex-grow-1">
                            <h6 class="fw-bold text-primary-custom mb-1" style="line-height: 1.3;">{{ $karya->judul }}</h6>
                            <small class="text-muted mb-3"><i class="bi bi-person-fill me-1"
                                    style="color: var(--steel-blue);"></i> {{ $karya->pembuat?->name ?? 'Siswa' }}</small>

                            <div
                                class="mt-auto pt-2 border-top border-custom d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-accent fs-6">Rp{{ number_format($karya->harga, 0, ',', '.') }}</span>
                                @if ($karya->stok > 0)
                                <small class="text-muted" style="font-size: 0.75rem;">Stok: {{ $karya->stok }}</small>
                                @else
                                <small class="text-danger" style="font-size: 0.75rem;">Stok Habis</small>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                <h5 class="text-muted mt-3">Belum ada karya yang sesuai dengan filter Anda.</h5>
            </div>
        @endforelse
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $karyas->links() }}
    </div>
@endsection
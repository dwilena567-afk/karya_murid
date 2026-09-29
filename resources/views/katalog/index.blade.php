@extends('layouts.main')

@section('content')
<div class="catalog-page">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary-custom m-0">Eksplorasi Karya</h4>
    </div>

    <!-- Grid Bootstrap -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        @forelse($karyas as $karya)
            <div class="col">
                <div class="product-card h-100 d-flex flex-column">
                    <a href="{{ route('katalog.show', $karya->id) }}"
                        class="text-decoration-none text-dark d-flex flex-column">

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
                            <h6 class="fw-bold text-primary-custom mb-1 title-clamp" style="line-height: 1.3;" title="{{ $karya->judul }}">{{ $karya->judul }}</h6>
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
                   
                        <div class="px-3 pb-3 mt-auto">
                            <form action="{{ route('keranjang.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="karya_id" value="{{ $karya->id }}">
                                <input type="hidden" name="jumlah" value="1">
                                <button type="submit" class="btn btn-primary btn-sm fw-bold w-100" {{ $karya->stok < 1 ? 'disabled' : '' }}>
                                    <i class="bi bi-cart-plus me-1"></i> Masukkan Keranjang
                                </button>
                            </form>
                        </div>
                   
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                <h5 class="text-muted mt-3">Tidak ada karya yang sesuai dengan filter Anda.</h5>
            </div>
        @endforelse
    </div>

    <div class="catalog-pagination d-flex justify-content-center">
        {{ $karyas->links() }}
    </div>
</div>

<style>
    .catalog-page {
        display: flex;
        flex-direction: column;
        min-height: 100%;
    }

    .catalog-pagination {
        margin-top: auto;
        padding-top: 1.5rem;
    }
</style>
@endsection
@extends('layouts.main')

@section('content')
    @php($gambarKarya = $karya->semua_gambar)
    <div class="container pb-5">

        
        <div class="mb-4">
            <a href="{{ route('katalog.index') }}" class="btn btn-outline-secondary btn-sm">
                &larr; Kembali ke Katalog
            </a>
        </div>

      
        <div class="card border-custom shadow-sm" style="border-radius: 10px;">
            <div class="card-body p-4 p-md-5">
                <div class="row g-4 align-items-center">

                  
                    <div class="col-md-6">
                        <div class="image-card clickable-gallery bg-light rounded-3 overflow-hidden text-center p-2 position-relative"
                            data-image-gallery>
                            <img data-gallery-main
                                src="{{ $gambarKarya->isNotEmpty() ? asset('storage/' . $gambarKarya->first()) : 'https://via.placeholder.com/450x450?text=Tidak+ada+gambar' }}"
                                alt="{{ $karya->judul }}"
                                class="img-fluid rounded-3 border border-custom object-fit-cover w-100"
                                style="max-height: 450px;">
                            @if($gambarKarya->count() > 1)
                                <button type="button" data-gallery-control="previous" aria-label="Gambar sebelumnya"
                                    class="btn btn-outline-secondary btn-sm fw-bold shadow-sm position-absolute top-50 start-0 translate-middle-y ms-3 rounded-2"
                                    style="width: 42px; height: 42px; z-index: 1; ">
                                    <span aria-hidden="true">&lt;</span>
                                    
                                </button>
                                <button type="button" data-gallery-control="next" aria-label="Gambar berikutnya"
                                    class="btn btn-outline-secondary btn-sm fw-bold shadow-sm position-absolute top-50 end-0 translate-middle-y me-3 rounded-2"
                                    style="width: 42px; height: 42px; z-index: 1; ">
                                    <span aria-hidden="true">&gt;</span>
                                </button>
                            @endif
                        </div>
                        @if($gambarKarya->count() > 1)
                            <div class="d-flex flex-wrap gap-2 mt-3">
                                @foreach($gambarKarya as $gambarPath)
                                    <button type="button" data-gallery-control="select" data-gallery-index="{{ $loop->index }}"
                                        aria-label="Tampilkan gambar {{ $loop->iteration }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                                        class="btn p-0 border-0 rounded">
                                        <img src="{{ asset('storage/' . $gambarPath) }}"
                                            alt="Gambar {{ $loop->iteration }} - {{ $karya->judul }}"
                                            class="rounded image-card border border-custom object-fit-cover"
                                            style="width: 72px; height: 72px;">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                 
                    <div class="col-md-6">
                        <span class="badge badge-category px-2 py-1">
                            {{ $karya->kategori->nama }}
                        </span>

                        <h1 class="fw-bold mb-3">{{ $karya->judul }}</h1>

                        <h3 class="text-accent fw-bold mb-4">
                            {{ $karya->harga == 0 ? 'Gratis' : 'Rp ' . number_format($karya->harga, 0, ',', '.') }}
                        </h3>

                        <div class="mb-4">
                            <h6 class="fw-bold text-dark">Deskripsi Karya</h6>
                            <p class="text-muted leading-relaxed">
                                {{ $karya->deskripsi }}
                            </p>
                        </div>

                        <div class="mb-4">
                            @if($karya->stok > 0)
                                <span class="text-secondary small">
                                    Stok Tersedia: <strong class="text-dark">{{ $karya->stok }}</strong>
                                </span>
                            @else
                                <span class="text-danger small">Stok Habis</span>
                            @endif
                        </div>

                        <hr class="my-4 border-custom opacity-100">

                       
                        <form action="{{ route('keranjang.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="karya_id" value="{{ $karya->id }}">

                            <div class="d-flex align-items-center gap-3">
                                @if($karya->stok > 0)
                                    <div style="width: 100px;">
                                        <input type="number" name="jumlah" value="1" min="1" max="{{ $karya->stok }}"
                                            class="form-control text-center" required>
                                    </div>
                                @endif

                                <button type="submit"
                                    class="btn {{ $karya->stok > 0 ? 'bg-accent' : 'btn-secondary' }} btn-sm fw-bold px-4 py-2 w-100"
                                    {{ $karya->stok < 1 ? 'disabled' : '' }}>
                                    <i class="bi bi-cart-plus me-1"></i> Masukkan Keranjang
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

       
        <section class="mt-5 pt-3">
           

            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
                @forelse($karyasRandom as $randomkarya)
                    <div class="col">
                        <a href="{{ route('katalog.show', $randomkarya->id) }}"
                            class="product-card h-100 d-flex flex-column text-decoration-none text-dark">
                            <div class="position-relative">
                                <img src="{{ $randomkarya->gambar ? asset('storage/' . $randomkarya->gambar) : 'https://via.placeholder.com/400x300' }}"
                                    class="w-100 object-fit-cover" style="height: 200px;" alt="{{ $randomkarya->judul }}">
                                <span class="badge badge-category position-absolute top-0 end-0 m-2 px-2 py-1 shadow-sm">
                                    {{ ucfirst($randomkarya->kategori?->nama ?? 'Umum') }}
                                </span>
                            </div>

                            <div class="p-3 d-flex flex-column flex-grow-1">
                                <h3 class="h6 fw-bold text-primary-custom mb-1 title-clamp">{{ $randomkarya->judul }}</h3>
                                <small class="text-muted mb-3">
                                    <i class="bi bi-person-fill me-1" style="color: var(--steel-blue);"></i>
                                    {{ $randomkarya->pembuat?->name ?? 'Siswa' }}
                                </small>

                                <div class="mt-auto pt-2 border-top border-custom d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-accent fs-6">{{ $randomkarya->harga == 0 ? 'Gratis' : 'Rp' . number_format($randomkarya->harga, 0, ',', '.') }}</span>
                                    @if ($randomkarya->stok > 0)
                                        <small class="text-muted" style="font-size: 0.75rem;">Stok: {{ $randomkarya->stok }}</small>
                                    @else
                                        <small class="text-danger" style="font-size: 0.75rem;">Stok Habis</small>
                                    @endif
                                </div>
                            </div>

                             <div class="px-3 pb-3 mt-auto">
                            <form action="{{ route('keranjang.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="karya_id" value="{{ $randomkarya->id }}">
                                <input type="hidden" name="jumlah" value="1">
                                <button type="submit" class="btn {{ $randomkarya->stok > 0 ? 'bg-accent' : 'btn-secondary' }} btn-sm fw-bold w-100" {{ $randomkarya->stok < 1 ? 'disabled' : '' }}>
                                    <i class="bi bi-cart-plus me-1"></i> Masukkan Keranjang
                                </button>
                            </form>
                        </div>
                        </a>

                       
                    </div>
                @empty
                    <div class="col-12">
                        <div class="feature-card p-4 text-center text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2" aria-hidden="true"></i>
                            Karya terbaru akan tampil di sini setelah tersedia.
                        </div>
                    </div>
                @endforelse
            </div>
        </section>

    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-image-gallery]').forEach(function (gallery) {
            const mainImage = gallery.querySelector('[data-gallery-main]');
            const thumbnails = gallery.parentElement.querySelectorAll('[data-gallery-index]');
            const images = Array.from(thumbnails, function (thumbnail) {
                return {
                    src: thumbnail.querySelector('img').src,
                    alt: thumbnail.querySelector('img').alt
                };
            });
            let activeIndex = 0;

            function showImage(index) {
                activeIndex = (index + images.length) % images.length;
                mainImage.src = images[activeIndex].src;
                mainImage.alt = images[activeIndex].alt;
                thumbnails.forEach(function (thumbnail, thumbnailIndex) {
                    thumbnail.setAttribute('aria-pressed', String(thumbnailIndex === activeIndex));
                });
            }

            gallery.querySelectorAll('[data-gallery-control]').forEach(function (control) {
                control.addEventListener('click', function (event) {
                    event.stopPropagation();
                    if (control.dataset.galleryControl === 'previous') {
                        showImage(activeIndex - 1);
                    } else if (control.dataset.galleryControl === 'next') {
                        showImage(activeIndex + 1);
                    } else {
                        showImage(Number(control.dataset.galleryIndex));
                    }
                });
            });

            gallery.parentElement.querySelectorAll('[data-gallery-index]').forEach(function (thumbnail) {
                thumbnail.addEventListener('click', function () {
                    showImage(Number(thumbnail.dataset.galleryIndex));
                });
            });
        });
    </script>
@endpush
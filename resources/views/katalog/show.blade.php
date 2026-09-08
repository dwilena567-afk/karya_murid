@extends('layouts.main')

@section('content')
<div class="container">
    <!-- Tombol Kembali -->
    <div class="mb-4">
        <a href="{{ route('katalog.index') }}" class="btn btn-outline-secondary btn-sm">
            &larr; Kembali ke Katalog
        </a>
    </div>

    <div class="card border-custom shadow-sm" style="border-radius: 10px;">
        <div class="card-body p-4 p-md-5">
            <div class="row g-4 align-items-center">
                <!-- Kolom Gambar -->
                <div class="col-md-6">
                    <div class="bg-light rounded-3 overflow-hidden text-center p-2">
                        <img src="{{ asset('storage/' . $karya->gambar) }}" 
                             alt="{{ $karya->judul }}" 
                             class="img-fluid rounded-3 object-fit-cover w-100" 
                             style="max-height: 450px;">
                    </div>
                </div>

                <!-- Kolom Detail & Aksi -->
                <div class="col-md-6">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2">
                        {{ $karya->kategori->nama }}
                    </span>
                    
                    <h1 class="fw-bold mb-3">{{ $karya->judul }}</h1>
                    
                    <h3 class="text-accent fw-bold mb-4">
                        
                        Rp {{ number_format($karya->harga, 0, ',', '.') }}
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
                        @elseif($karya->stok <= 0)
                        <span class="text-danger small">
                            Stok Habis
                        </span>
                        @endif
                    </div>

                    <hr class="my-4 text-muted opacity-25">

                    <!-- Form Tambah ke Keranjang -->
                    <form action="{{ route('keranjang.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="karya_id" value="{{ $karya->id }}">
                        
                        
                        <div class="d-flex align-items-center gap-3">
                             @if($karya->stok > 0)
                            <div style="width: 100px;">
                                <input type="number" 
                                       name="jumlah" 
                                       value="1" 
                                       min="1" 
                                       max="{{ $karya->stok }}" 
                                       class="form-control text-center" 
                                       required>
                            </div>
                            <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                                + Tambah ke Keranjang
                            </button>
                            @else
                            <button type="button" class="btn btn-secondary px-4 py-2 fw-semibold" disabled>
                                Stok Habis
                            </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
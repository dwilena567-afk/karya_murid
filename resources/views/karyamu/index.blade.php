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
        border-color: var(--steel-azure);
    }
    .badge-category {
        background-color: var(--wisteria-blue);
        color: white;
        font-weight: 500;
    }
    .action-buttons {
        border-top: 1px dashed var(--wisteria-blue);
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-primary-custom m-0">Kelola Karyamu</h4>
    
    <!-- Tombol Tambah Karya -->
    <a href="#" class="btn bg-accent fw-bold shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Tambah Karya
    </a>
</div>

<!-- Grid Bootstrap seperti di Katalog -->
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
    
    @forelse($karyas as $karya)
    <div class="col">
        <div class="product-card h-100 d-flex flex-column">
            
            <!-- Gambar Produk -->
            <div class="position-relative">
                <img src="{{ $karya->gambar ? asset('storage/'.$karya->gambar) : 'https://via.placeholder.com/400x300' }}" 
                     class="w-100 object-fit-cover" style="height: 200px;" alt="{{ $karya->judul }}">
                
                <!-- Lencana Kategori (Dilengkapi Nullsafe Operator) -->
                <span class="badge badge-category position-absolute top-0 end-0 m-2 px-2 py-1 shadow-sm">
                    {{ ucfirst($karya->kategori?->nama ?? 'Umum') }}
                </span>

                <!-- Lencana Status Verifikasi -->
                @if($karya->status_verifikasi == 'pending')
                    <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-2 px-2 py-1 shadow-sm"><i class="bi bi-hourglass-split me-1"></i>Menunggu</span>
                @elseif($karya->status_verifikasi == 'approved')
                    <span class="badge bg-success position-absolute top-0 start-0 m-2 px-2 py-1 shadow-sm"><i class="bi bi-check-circle me-1"></i>Disetujui</span>
                @else
                    <span class="badge bg-danger position-absolute top-0 start-0 m-2 px-2 py-1 shadow-sm"><i class="bi bi-x-circle me-1"></i>Ditolak</span>
                @endif
            </div>
            
            <!-- Badan Kartu -->
            <div class="p-3 d-flex flex-column flex-grow-1">
                <h6 class="fw-bold text-primary-custom mb-1" style="line-height: 1.3;">{{ $karya->judul }}</h6>
                
                <div class="mt-auto pt-2 border-top border-custom d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-bold text-accent fs-6">Rp{{ number_format($karya->harga, 0, ',', '.') }}</span>
                    <small class="text-muted" style="font-size: 0.75rem;">Stok: {{ $karya->stok }}</small>
                </div>

                <!-- Tombol Aksi (Edit & Hapus) -->
                <div class="mt-auto pt-3 action-buttons d-flex gap-2">
                    <a href="#" class="btn btn-sm btn-outline-primary flex-grow-1 fw-bold" title="Edit Karya">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                    </a>
                    <form action="#" method="POST" class="flex-grow-1 d-flex" onsubmit="return confirm('Apakah Anda yakin ingin menghapus karya ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100 fw-bold" title="Hapus Karya">
                            <i class="bi bi-trash me-1"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <!-- Tampilan Jika Data Kosong -->
    <div class="col-12 text-center py-5">
        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
         <h5 class="text-muted mt-3">Belum ada karya yang diunggah.</h5>
        
    </div>
    @endforelse
    
</div>
@endsection
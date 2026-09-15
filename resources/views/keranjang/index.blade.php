@extends('layouts.main')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-primary-custom m-0">Keranjang Belanja</h4>
</div>

@if($keranjangs->isEmpty())
    <!-- Tampilan jika keranjang kosong -->
    <div class="text-center py-5 ">
        <i class="bi bi-cart-x text-muted" style="font-size: 5rem;"></i>
        <h5 class="text-muted mt-3">Keranjang Anda masih kosong.</h5>
       
        <a href="{{ route('katalog.index') }}" class="btn bg-accent mt-3 fw-bold px-4 shadow-sm">
            Eksplorasi Karya
        </a>
    </div>
@else
    <!-- Tampilan jika keranjang ada isinya -->
    <div class="row g-4">
        <!-- Kolom Kiri: Daftar Produk -->
        <div class="col-lg-8">
            @php $totalHarga = 0; @endphp
            
            @foreach($keranjangs as $item)
                @php
                    // Menghitung subtotal per produk
                    $subtotal = $item->karya->harga * $item->jumlah;
                    $totalHarga += $subtotal;
                @endphp
                <div class="card mb-3 border-custom shadow-sm" style="border-radius: 10px; overflow: hidden;">
                    <div class="card-body d-flex flex-column flex-md-row align-items-center p-3">
                        
                        <!-- Gambar Produk -->
                        <img src="{{ $item->karya->gambar ? asset('storage/'.$item->karya->gambar) : 'https://via.placeholder.com/120' }}"
                             class="rounded object-fit-cover mb-3 mb-md-0 me-md-4 border border-custom" 
                             style="width: 120px; height: 120px;" 
                             alt="{{ $item->karya->judul }}">
                             
                        <!-- Detail Produk -->
                        <div class="flex-grow-1 text-center text-md-start w-100">
                            <h5 class="fw-bold text-primary-custom mb-1">{{ $item->karya->judul }}</h5>
                            <p class="text-muted small mb-2">
                                <i class="bi bi-person-fill me-1" style="color: var(--steel-blue);"></i> 
                                {{ $item->karya->pembuat?->name ?? 'Siswa' }}
                            </p>
                            <p class="mb-0 text-muted" style="font-size: 0.85rem;">
                                Rp{{ number_format($item->karya->harga, 0, ',', '.') }} <span class="mx-1">x</span> {{ $item->jumlah }} item
                            </p>
                        </div>
                        
                        <!-- Harga dan Aksi -->
                        <div class="text-center text-md-end mt-3 mt-md-0 ms-md-auto w-100" style="min-width: 150px;">
                            <h5 class="fw-bold text-accent mb-3">Rp{{ number_format($subtotal, 0, ',', '.') }}</h5>
                            
                            <!-- Tombol Hapus Terhubung dengan Controller -->
                            <form action="{{ route('keranjang.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus karya ini dari keranjang?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm fw-bold w-100 w-md-auto">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                        
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Kolom Kanan: Ringkasan Belanja -->
        <div class="col-lg-4">
            <div class="card border-custom shadow-sm position-sticky" style="top: 20px; border-radius: 10px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-primary-custom mb-3 border-bottom border-custom pb-3">
                        <i class="bi bi-receipt-cutoff me-2"></i>Ringkasan Belanja
                    </h5>
                    
                    <div class="d-flex justify-content-between mb-2 text-muted" style="font-size: 0.9rem;">
                        <span>Total Item</span>
                        <span>{{ $keranjangs->sum('jumlah') }} Barang</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-4 mt-3 pt-3 border-top border-custom">
                        <span class="fw-bold text-dark">Total Harga</span>
                        <span class="fw-bold text-accent fs-5">Rp{{ number_format($totalHarga, 0, ',', '.') }}</span>
                    </div>

                    <!-- Form Checkout Terhubung dengan TransaksiController -->
                    <form action="{{ route('transaksi.store') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn bg-accent w-100 fw-bold shadow-sm py-2" style="background-color: #ff9800; color: white; border: none;">
                            Checkout <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </form>
                    
                    
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
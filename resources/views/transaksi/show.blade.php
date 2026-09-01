@extends('layouts.main')

@section('content')
<div class="container py-4" style="max-width: 900px;">
     <div class="mb-4">
        <a href="{{ route('katalog.index') }}" class="btn btn-outline-secondary btn-sm">
            &larr; Kembali ke Katalog
        </a>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        
        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
            {{ ucfirst($transaksi->transaction_status ?? 'pending') }}
        </span>
    </div>

   

    <div class="card border-custom shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-light border-0 fw-bold text-primary-custom">
            Ringkasan Produk
        </div>
        <div class="card-body p-0">
            @foreach($transaksi->details as $detail)
                @php $karya = $detail->karya; @endphp
                <div class="d-flex align-items-center gap-3 border-bottom p-3">
                    <img src="{{ $karya && $karya->gambar ? asset('storage/' . $karya->gambar) : 'https://via.placeholder.com/150x150' }}"
                         alt="{{ $karya->judul ?? 'Produk' }}"
                         class="img-fluid rounded"
                         style="width: 90px; height: 90px; object-fit: cover;">

                    <div class="flex-grow-1">
                        <div class="fw-bold text-dark">{{ $karya->judul ?? 'Produk' }}</div>
                        <div class="small text-muted">
                            {{ $detail->jumlah }} x Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="fw-bold text-primary-custom">
                        Rp {{ number_format($detail->harga_satuan * $detail->jumlah, 0, ',', '.') }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

     <div class="card border-custom shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <small class="text-muted">Order ID</small>
                    <div class="fw-bold">{{ $transaksi->order_id }}</div>
                </div>
                <div class="col-md-6">
                    <small class="text-muted">Total Pembayaran</small>
                    <div class="fw-bold text-accent">Rp {{ number_format($transaksi->gross_amount, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    @if($transaksi->snap_token)
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>

        <div class="text-center mt-4">
            <button type="button" class="btn bg-primary-custom text-white btn-lg fw-bold px-4" onclick="snap.pay('{{ $transaksi->snap_token }}')">
                <i class="bi bi-credit-card me-2"></i> Bayar Sekarang
            </button>
        </div>
    @else
        <div class="alert alert-warning mt-4 mb-0">
            Token pembayaran belum tersedia untuk transaksi ini.
        </div>
    @endif
</div>
@endsection
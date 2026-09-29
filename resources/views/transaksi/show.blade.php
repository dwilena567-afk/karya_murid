@extends('layouts.main')

@section('content')
<div class="container py-4" style="max-width: 900px;">
     <div class="d-flex justify-content-between align-items-center mb-4 gap-3">
        <a href="{{ route('katalog.index') }}" class="btn btn-outline-secondary btn-sm">
            &larr; Kembali ke Katalog
        </a>
        <a href="{{ route('transaksi.index') }}" class="btn btn-outline-secondary btn-sm">
             Kembali ke Riwayat Transaksi &rarr;
        </a>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        
        @if(in_array($transaksi->transaction_status, ['settlement', 'capture']))
            <span class="badge bg-success px-3 py-2 rounded-pill">Berhasil</span>
        @elseif($transaksi->transaction_status === 'pending')
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Menunggu Pembayaran</span>
        @elseif($transaksi->transaction_status === 'expire')
            <span class="badge bg-danger px-3 py-2 rounded-pill">Gagal / Expired</span>
        @else
            <span class="badge bg-danger px-3 py-2 rounded-pill">Gagal</span>
        @endif
    </div>

   

    <div class="card border-custom shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
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

    @if($transaksi->snap_token && $transaksi->transaction_status === 'pending')
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>

        <div class="text-center mt-4">
            <button type="button" class="btn bg-primary-custom text-white btn-lg fw-bold px-4" onclick="bayarTransaksi()">
                <i class="bi bi-credit-card me-2"></i> Bayar Sekarang
            </button>
        </div>
        <script>
            function bayarTransaksi() {
                snap.pay('{{ $transaksi->snap_token }}', {
                    onSuccess: function (result) {
                        fetch('{{ route('transaksi.confirm-payment', $transaksi->id) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        })
                        .then(function (response) {
                            if (!response.ok) {
                                throw new Error('Konfirmasi pembayaran gagal.');
                            }
                            return response.json();
                        })
                        .then(function (payment) {
                            if (payment.successful) {
                                Swal.fire({ icon: 'success', title: 'Pembayaran Berhasil', text: 'Terima kasih atas transaksi Anda.', confirmButtonColor: '#054A91' })
                                    .then(function () { window.location.href = '{{ route('dashboard.index') }}'; });
                            }
                        })
                        .catch(function (error) {
                            Swal.fire({ icon: 'error', title: 'Konfirmasi Gagal', text: error.message, confirmButtonColor: '#054A91' });
                        });
                    },
                    onPending: function (result) {
                        Swal.fire({ icon: 'info', title: 'Pembayaran Diproses', text: 'Harap tunggu notifikasi dari sistem.', confirmButtonColor: '#054A91' });
                    },
                    onError: function (result) {
                        Swal.fire({ icon: 'error', title: 'Pembayaran Gagal', text: result.status_message + '. Silakan coba lagi atau hubungi support.', confirmButtonColor: '#054A91' });
                    },
                    onClose: function () {
                        Swal.fire({ icon: 'info', title: 'Pembayaran Belum Selesai', text: 'Popup pembayaran ditutup sebelum transaksi diselesaikan.', confirmButtonColor: '#054A91' });
                    }
                });
            }
        </script>
    @elseif($transaksi->transaction_status === 'expire')
        <div class="alert alert-danger mt-4 mb-0">
            Transaksi telah kedaluwarsa. Silakan buat transaksi baru untuk melanjutkan pembayaran.
        </div>
    @elseif($transaksi->transaction_status === 'pending')
        <div class="alert alert-warning mt-4 mb-0">
            Token pembayaran belum tersedia untuk transaksi ini.
        </div>
    @endif
</div>
@endsection
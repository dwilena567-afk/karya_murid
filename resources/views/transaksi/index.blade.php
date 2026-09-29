@extends('layouts.main')

@section('content')
@php($activeTab = request('tab', 'pembelian'))
<div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary-custom m-0">Riwayat Transaksi</h4>
    </div>
<div class="container py-2" style="max-width: 1000px;"></div>

<div class="container py-4 transaction-page">
   

    <!-- Navigasi Tabs -->
    <ul class="nav nav-pills mb-4 gap-2" id="riwayat-tab" role="tablist">
        <!-- Tab Pembelian Saya -->
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'pembelian' ? 'active' : '' }} fw-bold border border-custom" id="pembelian-tab" data-bs-toggle="pill" data-bs-target="#pembelian" type="button" role="tab" aria-controls="pembelian" aria-selected="{{ $activeTab === 'pembelian' ? 'true' : 'false' }}">
                <i class="bi bi-list-ul me-1"></i> Pembelian Saya
            </button>
        </li>
        
        <!-- Tab Penjualan Saya -->
        @if(auth()->user()->role === 'pembeli' || auth()->user()->role === 'admin')
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'penjualan' ? 'active' : '' }} fw-bold border border-custom" id="penjualan-tab" data-bs-toggle="pill" data-bs-target="#penjualan" type="button" role="tab" aria-controls="penjualan" aria-selected="{{ $activeTab === 'penjualan' ? 'true' : 'false' }}">
                <i class="bi bi-list-check me-1"></i> Penjualan Saya
            </button>
        </li>
        @endif

        <!-- Tab Penjualan Global (Khusus Admin) -->
        @if(auth()->user()->role === 'admin')
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'penjualan-global' ? 'active' : '' }} fw-bold border border-custom bg-warning-subtle text-dark" id="penjualan-global-tab" data-bs-toggle="pill" data-bs-target="#penjualan-global" type="button" role="tab" aria-controls="penjualan-global" aria-selected="{{ $activeTab === 'penjualan-global' ? 'true' : 'false' }}">
                <i class="bi bi-list me-1"></i> Riwayat Penjualan Global
            </button>
        </li>
        @endif
    </ul>

    <!-- Isi Tabs -->
    <div class="tab-content" id="riwayat-tabContent">
        
        <!-- TAB 1: PEMBELIAN SAYA -->
        <div class="tab-pane {{ $activeTab === 'pembelian' ? 'show active' : '' }}" id="pembelian" role="tabpanel" aria-labelledby="pembelian-tab">
            <div class="card border-custom shadow-sm" style="border-radius: 12px; overflow: hidden;">
                <div class="card-body p-6">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle m-0">
                            <thead class="bg-light border-bottom border-custom">
                                <tr>
                                    <th class="px-4 py-3 text-primary-custom">Tanggal / Order ID</th>
                                    <th class="py-3 text-primary-custom">Produk</th>
                                    <th class="py-3 text-primary-custom">Status Pembayaran</th>
                                    <th class="py-3 text-primary-custom">Total</th>
                                    <th class="px-4 py-3 text-primary-custom">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($pembelian as $trx)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($trx->created_at)->format('d M Y') }}</div>
                                        <span class="text-muted small">{{ $trx->order_id }}</span>
                                    </td>
                                    <td class="py-3">
                                        @foreach($trx->details as $detail)
                                            <div class="small {{ !$loop->last ? 'mb-1' : '' }}">
                                                <span class="fw-semibold text-dark title-clamp">{{ $detail->karya->judul ?? 'Karya Telah Dihapus' }}</span>
                                                <span class="text-muted">{{ $detail->jumlah }} x Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                    </td>
                                    <td class="py-3">
                                        @if(in_array($trx->transaction_status, ['settlement', 'capture']))
                                            <span class="badge bg-success px-3 py-2 rounded-pill">Berhasil</span>
                                        @elseif($trx->transaction_status === 'pending')
                                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Menunggu Pembayaran</span>
                                        @elseif($trx->transaction_status === 'expire')
                                            <span class="badge bg-danger px-3 py-2 rounded-pill">Gagal / Expired</span>
                                        @else
                                            <span class="badge bg-danger px-3 py-2 rounded-pill">Gagal</span>
                                        @endif
                                    </td>
                                    <td class="py-3 fw-bold text-accent">Rp {{ number_format($trx->gross_amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('transaksi.show', $trx->id) }}" class="btn bg-primary-custom text-white btn-sm fw-bold text-nowrap">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="bi bi-receipt text-muted" style="font-size: 3rem;"></i>
                                        <h5 class="text-muted mt-3">Belum ada riwayat pembelian.</h5>
                                        <a href="{{ route('katalog.index') }}" class="btn bg-accent mt-3 fw-bold px-4 shadow-sm text-white">Mulai Belanja</a>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @if($pembelian->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $pembelian->appends(['tab' => 'pembelian'])->links() }}
                </div>
            @endif
        </div>

        <!-- TAB 2: PENJUALAN SAYA  -->
        @if(auth()->user()->role === 'pembeli' || auth()->user()->role === 'admin')
        <div class="tab-pane {{ $activeTab === 'penjualan' ? 'show active' : '' }}" id="penjualan" role="tabpanel" aria-labelledby="penjualan-tab">
            <div class="card border-custom shadow-sm" style="border-radius: 12px; overflow: hidden;">
                <div class="card-body p-6">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle m-0">
                            <thead class="bg-light border-bottom border-custom">
                                <tr>
                                    <th class="px-4 py-3 text-primary-custom">Tanggal</th>
                                    <th class="py-3 text-primary-custom">Karya Terjual</th>
                                    <th class="py-3 text-primary-custom">Pembeli</th>
                                    <th class="py-3 text-primary-custom">Pendapatan</th>
                                    <th class="px-4 py-3 text-primary-custom">Status Pembayaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($penjualan as $jual)
                                    <tr>
                                        <td class="px-4 py-3 text-muted small">
                                            {{ \Carbon\Carbon::parse($jual->created_at)->format('d M Y, H:i') }}
                                        </td>
                                        <td class="py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="{{ $jual->karya && $jual->karya->gambar ? asset('storage/' . $jual->karya->gambar) : 'https://via.placeholder.com/50' }}" 
                                                     class="rounded object-fit-cover border border-custom" style="width: 50px; height: 50px;" alt="Karya">
                                                <div>
                                                    <h6 class="fw-bold mb-0 text-dark">{{ $jual->karya->judul ?? 'Karya Dihapus' }}</h6>
                                                    <span class="text-muted small">{{ $jual->jumlah }} item x Rp {{ number_format($jual->harga_satuan, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <span class="fw-medium text-dark"><i class="bi bi-person me-1"></i> {{ $jual->transaksi->user->name ?? 'Anonim' }}</span>
                                        </td>
                                        <td class="py-3 fw-bold text-accent">
                                            Rp {{ number_format($jual->jumlah * $jual->harga_satuan, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($jual->transaksi->transaction_status == 'settlement' || $jual->transaksi->transaction_status == 'capture')
                                                <span class="badge bg-success px-3 py-2 rounded-pill">Lunas</span>
                                            @elseif($jual->transaksi->transaction_status == 'pending')
                                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Pending</span>
                                            @elseif($jual->transaksi->transaction_status == 'expire')
                                                <span class="badge bg-danger px-3 py-2 rounded-pill">Gagal / Expired</span>
                                            @else
                                                <span class="badge bg-danger px-3 py-2 rounded-pill">Gagal</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                            <h5 class="text-muted mt-3">Belum ada karya Anda yang terjual.</h5>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($penjualan instanceof \Illuminate\Pagination\LengthAwarePaginator && $penjualan->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $penjualan->appends(['tab' => 'penjualan'])->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- TAB 3: RIWAYAT PENJUALAN GLOBAL (Khusus Admin) -->
        @if(auth()->user()->role === 'admin')
        <div class="tab-pane {{ $activeTab === 'penjualan-global' ? 'show active' : '' }}" id="penjualan-global" role="tabpanel" aria-labelledby="penjualan-global-tab">
            <div class="card border-custom shadow-sm" style="border-radius: 12px; overflow: hidden;">
                
                <div class="card-body p-6">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle m-0">
                            <thead class="bg-light border-bottom border-custom">
                                <tr>
                                    <th class="px-4 py-3 text-primary-custom">Tanggal</th>
                                    <th class="py-3 text-primary-custom">Karya</th>
                                    <th class="py-3 text-primary-custom">Penjual</th>
                                    <th class="py-3 text-primary-custom">Pembeli</th>
                                    <th class="py-3 text-primary-custom">Total</th>
                                    <th class="px-4 py-3 text-primary-custom">Status Pembayaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($penjualanGlobal as $global)
                                    <tr>
                                        <td class="px-4 py-3 text-muted small">
                                            {{ \Carbon\Carbon::parse($global->created_at)->format('d M Y, H:i') }}
                                        </td>
                                        <td class="py-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="{{ $global->karya && $global->karya->gambar ? asset('storage/' . $global->karya->gambar) : 'https://via.placeholder.com/40' }}" 
                                                     class="rounded object-fit-cover shadow-sm border border-custom" style="width: 40px; height: 40px;" alt="Karya">
                                                <span class="fw-bold text-dark">{{ $global->karya->judul ?? 'Karya Dihapus' }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                             <span class="fw-medium text-dark"><i class="bi bi-person me-1"></i> 
                                                {{ $global->karya->pembuat?->name ?? $global->karya->user?->name ?? 'Siswa' }}
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            <span class="fw-medium text-dark">
                                                <i class="bi bi-person me-1"></i> {{ $global->transaksi->user->name ?? 'Anonim' }}
                                            </span>
                                        </td>
                                        <td class="py-3 fw-bold text-accent">
                                            Rp {{ number_format($global->jumlah * $global->harga_satuan, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($global->transaksi->transaction_status == 'settlement' || $global->transaksi->transaction_status == 'capture')
                                                <span class="badge bg-success px-3 py-2 rounded-pill">Lunas</span>
                                            @elseif($global->transaksi->transaction_status == 'pending')
                                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Pending</span>
                                            @elseif($global->transaksi->transaction_status == 'expire')
                                                <span class="badge bg-danger px-3 py-2 rounded-pill">Gagal / Expired</span>
                                            @else
                                                <span class="badge bg-danger px-3 py-2 rounded-pill">Gagal</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                            <h5 class="text-muted mt-3">Belum ada transaksi penjualan di seluruh sistem.</h5>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($penjualanGlobal instanceof \Illuminate\Pagination\LengthAwarePaginator && $penjualanGlobal->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $penjualanGlobal->appends(['tab' => 'penjualan-global'])->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

<style>
    .transaction-page .btn {
        transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
    }

    .transaction-page .btn:not(:disabled):hover {
        transform: translateY(-1px);
        box-shadow: 0 0.25rem 0.5rem rgba(5, 74, 145, 0.2) !important;
    }

    .transaction-page .bg-primary-custom:hover {
        background-color: #043b73 !important;
        color: white !important;
    }

    .nav-pills .nav-link {
        color: var(--steel-azure);
        background-color: white;
        transition: all 0.3s ease;
    }
    
    .nav-pills .nav-link:hover {
        background-color: var(--alice-blue);
    }
    
    .nav-pills .nav-link.active {
        background-color: var(--steel-azure) !important;
        color: white !important;
        border-color: var(--steel-azure) !important;
    }

    #penjualan-global-tab.active {
        background-color: #ffc107 !important;
        color: #000 !important;
        border-color: #ffc107 !important;
    }
</style>
@endsection
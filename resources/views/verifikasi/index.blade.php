@extends('layouts.main')

@section('content')
 <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary-custom m-0">Verifikasi</h4>
    </div>
<div class="container py-2" style="max-width: 1000px;">


    <div class="card border-custom shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                    <thead class="bg-light border-bottom border-custom">
                        <tr>
                            <th class="px-4 py-3 text-primary-custom">Gambar</th>
                            <th class="py-3 text-primary-custom">Detail Karya</th>
                            <th class="py-3 text-primary-custom">Pembuat</th>
                            <th class="py-3 text-primary-custom">Harga</th>
                            <th class="px-4 py-3 text-center text-primary-custom">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($karyas as $karya)
                            <tr>
                                <td class="px-4 py-3">
                                    <img src="{{ asset('storage/' . $karya->gambar) }}" 
                                         alt="{{ $karya->judul }}" 
                                         class="rounded object-fit-cover shadow-sm border border-custom" 
                                         style="width: 80px; height: 80px;">
                                </td>
                                <td class="py-3">
                                    <h6 class="fw-bold mb-1 text-dark">{{ $karya->judul }}</h6>
                                    <span class="badge bg-secondary mb-2">{{ $karya->kategori->nama ?? 'Umum' }}</span>
                                    <p class="text-muted small mb-0 d-inline-block text-truncate" style="max-width: 250px;">
                                        {{ $karya->deskripsi }}
                                    </p>
                                </td>
                                <td class="py-3">
                                    <span class="fw-medium text-dark"><i class="bi bi-person me-1"></i> {{ $karya->pembuat->name ?? 'Anonim' }}</span>
                                </td>
                                <td class="py-3 fw-bold text-accent">
                                    Rp {{ number_format($karya->harga, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        
                                        <!-- Tombol Setujui -->
                                        <form action="{{ route('verifikasi.approve', $karya->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success fw-bold shadow-sm" title="Setujui Karya" onclick="return confirm('Yakin ingin menyetujui karya ini? Karya akan langsung tampil di katalog publik.');">
                                                <i class="bi bi-check-lg me-1"></i> Setujui
                                            </button>
                                        </form>
                                        
                                        <!-- Tombol Tolak -->
                                        <form action="{{ route('verifikasi.reject', $karya->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-danger fw-bold shadow-sm" title="Tolak Karya" onclick="return confirm('Tolak karya ini? Karya tidak akan ditampilkan di katalog.');">
                                                <i class="bi bi-x-lg me-1"></i> Tolak
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="bi bi-patch-check text-muted" style="font-size: 4rem;"></i>
                                    <h5 class="text-muted mt-3">Tidak ada karya yang menunggu verifikasi.</h5>
                                   
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.main')

@section('content')

      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary-custom m-0">Dashboard</h4>
    </div>

<div class="container py-3 dashboard-page">
    <!-- Summary Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 position-relative overflow-hidden">
                <!-- Aksen warna samping -->
                <div class="position-absolute top-0 start-0 h-100" style="width: 5px; background-color: var(--steel-azure);"></div>
                <div class="card-body p-4 ms-2 d-flex justify-content-between align-items-center">
                    <div class="w-100">
                        <p class="text-muted fw-bold mb-1" style="font-size: 0.85rem; letter-spacing: 0.5px;">TOTAL KARYA</p>
                        <h3 class="fw-bold text-dark m-0">{{ number_format($totalKarya) }}</h3>
                        

                        <div class="mt-3">
                            <a href="{{ route('karyamu.index') }}" class="btn btn-sm bg-primary-custom text-white fw-bold px-3">
                                <i class="bi bi-pencil-square me-1"></i>Kelola Karyamu
                            </a>

                        </div>

                    </div>
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-image text-primary-custom fs-1"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 position-relative overflow-hidden">
                <div class="position-absolute top-0 start-0 h-100" style="width: 5px; background-color: var(--wisteria-blue);"></div>
                <div class="card-body p-4 ms-2 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted fw-bold mb-1" style="font-size: 0.85rem; letter-spacing: 0.5px;">KARYA TERJUAL</p>
                        <h3 class="fw-bold text-dark m-0">{{ number_format($totalTerjual) }} <small class="text-muted fs-6">Item</small></h3>

                        <div class="mt-3">
                            <a href="{{ route('transaksi.index') }}" class="btn btn-sm bg-primary-custom text-white fw-bold px-3">
                                <i class="bi bi-archive me-1"></i>Riwayat Transaksi

                            </a>

                        </div>
                    </div>
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-cart-check fs-1" style="color: var(--wisteria-blue); font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 position-relative overflow-hidden">
                <div class="position-absolute top-0 start-0 h-100" style="width: 5px; background-color: var(--harvest-orange);"></div>
                <div class="card-body p-4 ms-2 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted fw-bold mb-1" style="font-size: 0.85rem; letter-spacing: 0.5px;">TOTAL PENDAPATAN</p>
                        <h3 class="fw-bold text-accent m-0">Rp{{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                    </div>
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-cash-stack text-accent fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->role === 'admin')
        <section class="mb-5" id="verifikasi">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-primary-custom m-0">
                    Verifikasi Karya
                </h5>
                <span class="badge bg-warning text-dark">{{ $karyasMenungguVerifikasi->total() }} Menunggu</span>
            </div>

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
                                @forelse($karyasMenungguVerifikasi as $karya)
                                    <tr>
                                        <td class="px-4 py-3">
                                           
                                                <img src="{{ $karya->gambar ? asset('storage/' . $karya->gambar) : 'https://via.placeholder.com/450x450?text=Tidak+ada+gambar' }}"
                                                    alt="{{ $karya->judul }}"
                                                    class="rounded image-card clickable-gallery object-fit-cover shadow-sm border border-custom"
                                                    style="width: 80px; height: 80px;">
                                                @foreach($karya->gambarTambahan as $gambar)
                                                    <img src="{{ asset('storage/' . $gambar->gambar) }}"
                                                        alt="Foto tambahan {{ $loop->iteration }} - {{ $karya->judul }}"
                                                        class="rounded image-card clickable-gallery object-fit-cover shadow-sm border border-custom mt-1"
                                                        style="width: 80px; height: 80px;">
                                                @endforeach
                                           
                                        </td>
                                        <td class="py-3">
                                            <h6 class="fw-bold mb-1 text-dark">{{ $karya->judul }}</h6>
                                            <span class="badge bg-secondary mb-2">{{ $karya->kategori?->nama ?? 'Umum' }}</span>
                                        </td>
                                        <td class="py-3">
                                            <span class="fw-medium text-dark">
                                                <i class="bi bi-person me-1"></i>{{ $karya->pembuat?->name ?? 'Anonim' }}
                                            </span>
                                        </td>
                                        <td class="py-3 fw-bold text-accent">
                                            {{ $karya->harga == 0 ? 'Gratis' : 'Rp ' . number_format($karya->harga, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <form action="{{ route('verifikasi.approve', $karya->id) }}" method="POST"
                                                    data-confirm="Karya akan langsung tampil di katalog publik setelah disetujui."
                                                    data-confirm-title="Setujui Karya">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-success fw-bold shadow-sm" title="Setujui Karya">
                                                        <i class="bi bi-check-lg me-1"></i> Setujui
                                                    </button>
                                                </form>
                                                <form action="{{ route('verifikasi.reject', $karya->id) }}" method="POST"
                                                    data-confirm="Karya ini tidak akan ditampilkan di katalog."
                                                    data-confirm-title="Tolak Karya">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger fw-bold shadow-sm" title="Tolak Karya">
                                                        <i class="bi bi-x-lg me-1"></i> Tolak
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="bi bi-patch-check text-muted" style="font-size: 3rem;"></i>
                                            <h6 class="text-muted mt-3 mb-0">Tidak ada karya yang menunggu verifikasi.</h6>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($karyasMenungguVerifikasi->hasPages())
                        <div class="d-flex justify-content-center p-3">
                            {{ $karyasMenungguVerifikasi->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    <!-- Chart Section -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
        <h5 class="fw-bold text-primary-custom m-0">
            Tren Penjualan ({{ $durasiTren['label'] }})
        </h5>
        <form method="GET" action="{{ route('dashboard.index') }}" class="d-flex align-items-center gap-2">
            <input type="hidden" name="periode_populer" value="{{ $periodePopuler }}">
            <label for="periode-tren" class="form-label mb-0 text-muted small">Durasi:</label>
            <select id="periode-tren" name="periode" class="form-select form-select-sm border-custom" onchange="this.form.submit()">
                <option value="7_minggu" {{ $periode === '7_minggu' ? 'selected' : '' }}>7 Minggu</option>
                <option value="1_bulan" {{ $periode === '1_bulan' ? 'selected' : '' }}>1 Bulan</option>
                <option value="1_tahun" {{ $periode === '1_tahun' ? 'selected' : '' }}>1 Tahun</option>
                <option value="all_time" {{ $periode === 'all_time' ? 'selected' : '' }}>All Time</option>
            </select>
            @if($periode !== '7_minggu')
                <a href="{{ route('dashboard.index', ['periode' => '7_minggu', 'periode_populer' => $periodePopuler]) }}" class="small text-nowrap">Reset</a>
            @endif
        </form>
    </div>
    <p class="text-muted small mb-3">
        Rentang tanggal:
        {{ $durasiTren['mulai'] ? $durasiTren['mulai']->format('d/m/Y') . ' – ' . $durasiTren['sampai']->format('d/m/Y') : 'Semua tanggal' }}
    </p>
    <div class="row">
        <div class="col-12">
            <div class="card border-custom shadow-sm rounded-3">

                <div class="card-body p-4">
                    @if(count($chartLabels))
                        <canvas id="salesChart" height="100"></canvas>
                    @else
                        <div class="py-4 text-center" role="status">
                            <p class="fw-semibold mb-1">Belum ada penjualan pada rentang tanggal ini.</p>
                            <p class="text-muted small mb-3">Coba rentang tanggal lain, atau kelola dan promosikan karyamu agar mulai ditemukan pembeli.</p>
                            <a href="{{ route('karyamu.index') }}" class="btn btn-sm bg-primary-custom text-white fw-bold px-3">Kelola Karyamu</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <section class="mt-5" aria-labelledby="karya-terpopuler-title">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
                <h5 id="karya-terpopuler-title" class="fw-bold text-primary-custom m-0">
                    Karya Paling Populer ({{ $durasiPopuler['label'] }})
                </h5>
                <form method="GET" action="{{ route('dashboard.index') }}" class="d-flex align-items-center gap-2">
                    <input type="hidden" name="periode" value="{{ $periode }}">
                    <label for="periode-populer" class="form-label mb-0 text-muted small">Durasi:</label>
                    <select id="periode-populer" name="periode_populer" class="form-select form-select-sm border-custom" onchange="this.form.submit()">
                        <option value="7_minggu" {{ $periodePopuler === '7_minggu' ? 'selected' : '' }}>7 Minggu</option>
                        <option value="1_bulan" {{ $periodePopuler === '1_bulan' ? 'selected' : '' }}>1 Bulan</option>
                        <option value="1_tahun" {{ $periodePopuler === '1_tahun' ? 'selected' : '' }}>1 Tahun</option>
                        <option value="all_time" {{ $periodePopuler === 'all_time' ? 'selected' : '' }}>All Time</option>
                    </select>
                    @if($periodePopuler !== '7_minggu')
                        <a href="{{ route('dashboard.index', ['periode' => $periode, 'periode_populer' => '7_minggu']) }}" class="small text-nowrap">Reset</a>
                    @endif
                </form>
            </div>
            <p class="text-muted small mb-3">
                Rentang tanggal:
                {{ $durasiPopuler['mulai'] ? $durasiPopuler['mulai']->format('d/m/Y') . ' – ' . $durasiPopuler['sampai']->format('d/m/Y') : 'Semua tanggal' }}
            </p>
            <div class="card border-custom shadow-sm rounded-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0">
                        <thead class="bg-light border-bottom border-custom">
                            <tr>
                                <th class="px-4 py-3 text-primary-custom">No</th>
                                <th class="py-3 text-primary-custom">Karya</th>
                                <th class="px-4 py-3 text-end text-primary-custom">Terjual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($karyaTerpopuler as $karya)
                                <tr>
                                    <td class="px-4 py-3 fw-bold text-muted">{{ $loop->iteration }}</td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center gap-3">
                                        
                                                <img src="{{ $karya->gambar ? asset('storage/' . $karya->gambar) : 'https://via.placeholder.com/450x450?text=Tidak+ada+gambar' }}" 
                                                    class="rounded object-fit-cover border border-custom"
                                                    style="width: 56px; height: 56px;">
                                          
                                            <span class="fw-semibold text-dark">{{ $karya->judul }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-end fw-bold text-accent">
                                        {{ number_format($karya->total_terjual) }} item
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-4 text-center" role="status">
                                        <p class="fw-semibold mb-1">Belum ada karya terjual pada rentang tanggal ini.</p>
                                        <p class="text-muted small mb-3">Coba rentang tanggal lain, atau kelola dan promosikan karyamu agar mulai ditemukan pembeli.</p>
                                        <a href="{{ route('karyamu.index') }}" class="btn btn-sm bg-primary-custom text-white fw-bold px-3">Kelola Karyamu</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<style>
    .dashboard-page .btn {
        transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
    }

    .dashboard-page .btn:not(:disabled):hover {
        transform: translateY(-1px);
        box-shadow: 0 0.25rem 0.5rem rgba(5, 74, 145, 0.2) !important;
    }

    .dashboard-page .bg-primary-custom:hover {
        background-color: #043b73 !important;
        color: white !important;
    }
</style>


<!-- Import library Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Menerima data dari Controller
        const labels = {!! json_encode($chartLabels) !!};
        const dataValues = {!! json_encode($chartData) !!};
        const canvas = document.getElementById('salesChart');

        if (!canvas || labels.length === 0) {
            return;
        }

        const ctx = canvas.getContext('2d');
        
        //  Grafik Garis
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: dataValues,
                    borderColor: '#F17300', // Sesuai var(--harvest-orange)
                    backgroundColor: 'rgba(241, 115, 0, 0.1)', 
                    borderWidth: 2,
                    pointBackgroundColor: '#054A91', // Sesuai var(--steel-azure)
                    pointBorderColor: '#fff',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.3 // Memberikan efek lengkungan halus pada garis
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false // Sembunyikan legenda jika hanya satu dataset
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000) + ' Jt';
                                } else if (value >= 1000) {
                                    return 'Rp ' + (value / 1000) + ' Rb';
                                }
                                return 'Rp ' + value;
                            }
                        }
                    }
                }
            }
        });
    });
</script>


@endsection
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

    <!-- Chart Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-custom shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom border-custom p-3">
                    <h6 class="fw-bold text-primary-custom m-0"><i class="bi bi-graph-up me-2"></i>Tren Penjualan (7 Hari Terakhir)</h6>
                </div>
                <div class="card-body p-4">
                    <!-- Kanvas untuk Chart.js -->
                    <canvas id="salesChart" height="100"></canvas>
                </div>
            </div>
        </div>
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

        const ctx = document.getElementById('salesChart').getContext('2d');
        
        // Membangun Grafik Garis
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
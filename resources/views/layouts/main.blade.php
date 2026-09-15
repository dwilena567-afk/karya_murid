<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Katalog Karya</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        /* Mendaftarkan Palet Warna */
        :root {
            --steel-azure: #054A91;
            --steel-blue: #3E7CB1;
            --wisteria-blue: #81A4CD;
            --alice-blue: #DBE4EE;
            --harvest-orange: #F17300;
        }

        /* Penerapan Tema Global dengan Font Verdana */
        html,
        body {
            background-color: var(--alice-blue);
            color: #333;
            font-family: Verdana, Geneva, Tahoma, sans-serif;
            overflow-y: auto;
            scrollbar-width: none;
            /* Firefox */
            -ms-overflow-style: none;
            /* IE 10+ */
        }

        html::-webkit-scrollbar,
        body::-webkit-scrollbar {
            display: none;
            /* Chrome, Safari, Opera */
        }

        .bg-primary-custom {
            background-color: var(--steel-azure) !important;
        }

        .text-primary-custom {
            color: var(--steel-azure) !important;
        }

        .bg-accent {
            background-color: var(--harvest-orange) !important;
            color: white !important;
            transition: 0.3s;
        }

        .bg-accent:hover {
            background-color: #d96600 !important;
            color: white !important;
        }

        .text-accent {
            color: var(--harvest-orange) !important;
        }

        .border-custom {
            border-color: var(--wisteria-blue) !important;
        }

        .nav-link-custom {
            color: var(--alice-blue);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-link-custom:hover {
            color: var(--harvest-orange);
        }

        .sidebar-panel {
            background-color: white;
            border-right: 1px solid var(--wisteria-blue);
            width: 260px;
        }

        /* Mengubah warna teks dan border tombol pagination */
        .pagination .page-link {
            color: var(--steel-azure);
            border-color: var(--wisteria-blue);
        }

        /* Mengubah warna saat tombol di-hover */
        .pagination .page-link:hover {
            background-color: var(--alice-blue);
            color: var(--steel-azure);
        }

        /* Mengubah warna tombol halaman yang sedang aktif */
        .pagination .page-item.active .page-link {
            background-color: var(--steel-azure);
            border-color: var(--steel-azure);
            color: white;
        }

        /* Mengubah warna tombol yang tidak bisa diklik (disabled) */
        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #e9ecef;
        }

        .pagination {
            margin-left: 1.5rem !important;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">

    <!-- Top Navigation Bar -->
    <nav class="bg-primary-custom px-4 py-2 d-flex justify-content-between align-items-center shadow-sm">
        <div class="d-flex gap-4">
            <a href="{{ route('beranda.index') }}" class="nav-link-custom"><i
                    class="bi bi-house-door me-1"></i>Beranda</a>
            <a href="{{ route('katalog.index') }}" class="nav-link-custom"><i class="bi bi-grid me-1"></i>Katalog</a>
            @auth
                <a href="{{ route('keranjang.index') }}" class="nav-link-custom"><i
                        class="bi bi-cart3 me-1"></i>Keranjang</a>
                <a href="{{ route('karyamu.index') }}" class="nav-link-custom"><i class="bi bi-palette me-1"></i>Karyamu</a>
                <a href="{{ route('dashboard.index') }}" class="nav-link-custom"><i
                        class="bi bi-speedometer2 me-1"></i>Dashboard</a>
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('verifikasi.index') }}" class="nav-link-custom">
                        <i class="bi bi-shield-check me-1"></i>Verifikasi
                    </a>
                @endif
            @endauth
        </div>
        <div>
            @auth
                <div class="d-flex align-items-center gap-3">
                    <span class="text-white small">Halo, <strong>{{ auth()->user()->name }}</strong></span>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm py-1 px-2"
                            style="font-size: 0.75rem;">Logout</button>
                    </form>
                </div>
            @else
                
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm py-1 px-3 fw-bold" style="font-size: 0.8rem;">
                    Sign In <i class="bi bi-box-arrow-in-right ms-1"></i>
                </a>

                <a href="{{ route('register') }}" class="btn bg-accent btn-sm py-1 px-3 fw-bold" style="font-size: 0.8rem;">
                    Sign Up <i class="bi bi-person-plus ms-1"></i> 
                </a>
            @endauth
        </div>
    </nav>

    <!-- Content Wrapper -->
    <div class="container-fluid flex-grow-1 p-0 d-flex">

        <!-- Sidebar Filter -->
        @if(request()->routeIs('katalog.index'))

            <aside class="sidebar-panel p-4 d-none d-md-block shadow-sm">
                <h6 class="fw-bold text-primary-custom mb-3 border-bottom border-custom pb-2"><i
                        class="bi bi-search me-1"></i> Cari & Filter</h6>
                <!-- Consolidated Form: Combines search, category, price, and sort -->
                <form action="{{ request()->url() }}" method="GET" class="d-flex flex-column gap-3 small">

                    <!-- Search Input -->
                    <div>
                        <label class="form-label text-muted fw-bold mb-1" style="font-size: 0.75rem;">Cari Produk</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" class="form-control border-custom" placeholder="Nama produk..."
                                value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary border-custom" type="submit"
                                style="color: var(--steel-blue);"><i class="bi bi-search"></i></button>
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label class="form-label text-muted fw-bold mb-1" style="font-size: 0.75rem;">Kategori</label>
                        <select name="kategori" id="kategori_id" class="form-select form-select-sm border-custom">
                            <option value="">Semua Kategori</option>

                            @if(isset($kategoris))
                                @foreach($kategoris as $kat)
                                    <option value="{{ $kat->id }}" {{ request('kategori') == $kat->id ? 'selected' : '' }}>
                                        {{ $kat->nama }}
                                    </option>
                                @endforeach
                            @endif

                        </select>
                    </div>

                    <!-- Price Range Filter -->
                    <div>
                        <label class="form-label text-muted fw-bold mb-1" style="font-size: 0.75rem;">Rentang Harga</label>
                        <input type="number" name="min_harga" class="form-control form-control-sm border-custom mb-2"
                            placeholder="Min Rp" value="{{ request('min_harga') }}">
                        <input type="number" name="max_harga" class="form-control form-control-sm border-custom"
                            placeholder="Max Rp" value="{{ request('max_harga') }}">
                    </div>

                    <!-- Sort Dropdown -->
                    <div>
                        <label class="form-label text-muted fw-bold mb-1" style="font-size: 0.75rem;">Urutkan
                            Berdasarkan</label>
                        <select name="sort" class="form-select form-select-sm border-custom">
                            <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                            <option value="termurah" {{ request('sort') == 'termurah' ? 'selected' : '' }}>Termurah</option>
                            <option value="termahal" {{ request('sort') == 'termahal' ? 'selected' : '' }}>Termahal</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-column gap-2 mt-2">
                        <button type="submit" class="btn bg-primary-custom text-white btn-sm fw-bold">Terapkan
                            Filter</button>
                        <a href="{{ request()->url() }}" class="btn btn-outline-danger btn-sm">Reset</a>
                    </div>
                </form>
            </aside>
        @endif

        <!-- Main Content Area -->
        <main class="flex-grow-1 p-4">
            @if(session('success'))
                <div class="alert alert-success py-2 mb-4 shadow-sm border-0"><i
                        class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger py-2 mb-4 shadow-sm border-0"><i
                        class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}</div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Footer -->
    <footer class="bg-primary-custom text-center py-4 mt-auto">
        <p class="m-0 text-white small" style="opacity: 0.8;">&copy; {{ date('Y') }} TEST BUILD. BUKAN PRODUK FINAL.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
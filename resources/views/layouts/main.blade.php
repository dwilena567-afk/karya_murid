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
            display: flex;
            align-items: center;
            width: 100%;
            padding: 0.7rem 0.8rem;
            border-radius: 0.375rem;
            color: var(--alice-blue);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: background-color 0.2s, color 0.2s;
        }

        .nav-link-custom:hover,
        .nav-link-custom.active {
            background-color: rgba(255, 255, 255, 0.12);
            color: white;
        }

        .app-sidebar {
            width: 240px;
            flex: 0 0 240px;
            min-height: 100vh;
            background-color: var(--steel-azure);
        }

        .app-page {
            min-width: 0;
        }

        @media (max-width: 767.98px) {
            body {
                flex-direction: column;
            }

            .app-sidebar {
                width: 100%;
                flex: 0 0 auto;
                min-height: 0;
            }

            .app-nav-links {
                flex-direction: row !important;
                flex-wrap: wrap;
            }

            .nav-link-custom {
                width: auto;
                flex: 1 1 auto;
            }

            .app-account {
                margin-top: 0.75rem !important;
                padding-top: 0.75rem !important;
                border-top: 1px solid rgba(255, 255, 255, 0.2);
            }
        }

        .sidebar-panel {
            background-color: white;
            border-right: 1px solid var(--wisteria-blue);
            width: 260px;
            flex: 0 0 260px;
            transition: width 0.2s ease, flex-basis 0.2s ease, padding 0.2s ease;
        }

        .sidebar-panel.is-collapsed {
            width: 64px;
            flex-basis: 64px;
            padding: 0.75rem !important;
        }

        .filter-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
        }

        .filter-heading-title {
            white-space: nowrap;
        }

        .sidebar-panel.is-collapsed .filter-heading-title {
            display: none;
        }

        .sidebar-panel.is-collapsed .filter-content {
            display: none;
        }

        .filter-toggle {
            flex: 0 0 auto;
        }

        .sidebar-panel.is-collapsed .filter-toggle {
            margin: 0 auto;
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

     .image-card {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .image-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(62, 124, 177, 0.2);
        border-color: var(--steel-azure);
    }

    .badge-category {
        background-color: var(--wisteria-blue);
        color: white;
        font-weight: 500;
    }
    .title-clamp {
        display: -webkit-box;
        width: 100%;
        max-height: 2.6em;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        overflow: hidden;
        text-overflow: ellipsis;
        overflow-wrap: anywhere;
    }
    .action-buttons {
        border-top: 1px dashed var(--wisteria-blue);
    }
    </style>
</head>

<body class="d-flex min-vh-100">

    <aside class="app-sidebar d-flex flex-column p-3 shadow-sm">
        <a href="{{ route('beranda.index') }}" class="text-white text-decoration-none fw-bold fs-5 mb-4 px-2">
            <i class="bi bi-palette me-2"></i>Karya Murid
        </a>

        <nav class="app-nav-links d-flex flex-column gap-1">
            <a href="{{ route('beranda.index') }}" class="nav-link-custom {{ request()->routeIs('beranda.*') ? 'active' : '' }}">
                <i class="bi bi-house-door me-2"></i>Beranda
            </a>
            <a href="{{ route('katalog.index') }}" class="nav-link-custom {{ request()->routeIs('katalog.*') ? 'active' : '' }}">
                <i class="bi bi-grid me-2"></i>Katalog
            </a>
            @auth
                <a href="{{ route('keranjang.index') }}" class="nav-link-custom {{ request()->routeIs('keranjang.*') ? 'active' : '' }}">
                    <i class="bi bi-cart3 me-2"></i>Keranjang
                </a>
                <a href="{{ route('transaksi.index') }}" class="nav-link-custom {{ request()->routeIs('transaksi.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-left-right me-2"></i>Transaksi
                </a>
                <a href="{{ route('karyamu.index') }}" class="nav-link-custom {{ request()->routeIs('karyamu.*') ? 'active' : '' }}">
                    <i class="bi bi-palette me-2"></i>Karyamu
                </a>
                <a href="{{ route('dashboard.index') }}" class="nav-link-custom {{ request()->routeIs('dashboard.*') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('verifikasi.index') }}" class="nav-link-custom {{ request()->routeIs('verifikasi.*') ? 'active' : '' }}">
                        <i class="bi bi-shield-check me-2"></i>Verifikasi
                    </a>
                @endif
            @endauth
        </nav>

        <div class="app-account mt-auto pt-4">
            @auth
                <div class="text-white small mb-2 px-2">Halo, <strong>{{ auth()->user()->name }}</strong></div>
                <form method="POST" action="{{ route('logout') }}" class="px-2" data-confirm="Keluar dari akun sekarang?" data-confirm-title="Konfirmasi Logout">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm w-100">Logout</button>
                </form>
            @else
                <div class="d-flex flex-column gap-2 px-2">
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm fw-bold">Sign In</a>
                    <a href="{{ route('register') }}" class="btn bg-accent btn-sm fw-bold">Sign Up</a>
                </div>
            @endauth
        </div>
    </aside>

    <div class="app-page flex-grow-1 d-flex flex-column min-vh-100">
    <!-- Content Wrapper -->
    <div class="container-fluid flex-grow-1 p-0 d-flex">

        <!-- Sidebar Filter -->
        @if(request()->routeIs('katalog.index') || request()->routeIs('karyamu.index'))

            <aside class="sidebar-panel p-4 d-none d-md-block shadow-sm" id="filter-sidebar">
                <div class="filter-heading fw-bold text-primary-custom mb-3 border-bottom border-custom pb-2">
                    <h6 class="filter-heading-title fw-bold mb-0"><i class="bi bi-search me-1"></i> Cari & Filter</h6>
                    <button type="button" class="btn btn-sm btn-outline-secondary filter-toggle" id="filter-sidebar-toggle"
                        aria-label="Ciutkan panel filter" aria-controls="filter-content" aria-expanded="true" title="Ciutkan panel filter">
                        <i class="bi bi-chevron-left" aria-hidden="true"></i>
                    </button>
                </div>
                <!-- Consolidated Form: Combines search, category, price, and sort -->
                <div class="filter-content" id="filter-content">
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

                    @if(request()->routeIs('katalog.index'))
                    <!-- Seller Name Search Input -->
                    <div>
                        <label class="form-label text-muted fw-bold mb-1" style="font-size: 0.75rem;">Cari Pembuat</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="pembuat" class="form-control border-custom" placeholder="Nama pembuat..."
                                value="{{ request('pembuat') }}">
                            <button class="btn btn-outline-secondary border-custom" type="submit"
                                style="color: var(--steel-blue);"><i class="bi bi-search"></i></button>
                        </div>
                    </div>
                    @endif

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
                </div>
            </aside>
        @endif

        <!-- Main Content Area -->
        <main class="flex-grow-1 p-4">
            @yield('content')
        </main>
    </div>

    <!-- Footer -->
    <footer class="bg-primary-custom text-center py-4 mt-auto">
        <p class="m-0 text-white small" style="opacity: 0.8;">&copy; {{ date('Y') }} TEST BUILD. BUKAN PRODUK FINAL.</p>
    </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Berhasil', text: @json(session('success')), confirmButtonColor: '#054A91' });
        @elseif(session('error'))
            Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: @json(session('error')), confirmButtonColor: '#054A91' });
        @endif

        document.addEventListener('submit', function (event) {
            const form = event.target;
            if (!form.matches('form[data-confirm]') || form.dataset.confirmed === 'true') return;

            event.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: form.dataset.confirmTitle || 'Konfirmasi',
                text: form.dataset.confirm,
                showCancelButton: true,
                confirmButtonText: 'Ya, lanjutkan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#054A91',
                cancelButtonColor: '#6c757d'
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.dataset.confirmed = 'true';
                    form.requestSubmit();
                }
            });
        });

        (() => {
            const sidebar = document.getElementById('filter-sidebar');
            const toggle = document.getElementById('filter-sidebar-toggle');

            if (!sidebar || !toggle) return;

            const icon = toggle.querySelector('i');
            const storageKey = 'filter-sidebar-collapsed';

            const setCollapsed = (collapsed) => {
                sidebar.classList.toggle('is-collapsed', collapsed);
                toggle.setAttribute('aria-expanded', String(!collapsed));
                toggle.setAttribute('aria-label', collapsed ? 'Buka panel filter' : 'Ciutkan panel filter');
                toggle.title = collapsed ? 'Buka panel filter' : 'Ciutkan panel filter';
                icon.className = collapsed ? 'bi bi-chevron-right' : 'bi bi-chevron-left';
            };

            setCollapsed(localStorage.getItem(storageKey) === 'true');
            toggle.addEventListener('click', () => {
                const collapsed = !sidebar.classList.contains('is-collapsed');
                setCollapsed(collapsed);
                localStorage.setItem(storageKey, String(collapsed));
            });
        })();
    </script>
    <script>
        const images = document.querySelectorAll('.clickable-gallery');

        images.forEach(img => {
            img.addEventListener('click', function () {
                Swal.fire({
                    imageUrl: this.querySelector('img').src,
                    imageWidth: 700,
                    width: '750px',
                   
                    showConfirmButton: false
                });
            });
        });
    </script>
</body>

</html>
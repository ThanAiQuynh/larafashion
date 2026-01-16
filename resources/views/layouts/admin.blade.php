<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - LaraFashion</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #6366f1;
            --primary-dark: #4f46e5;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #f1f5f9;
        }

        /* Fix modal scrollbar shift */
        body.modal-open {
            overflow: auto !important;
            padding-right: 0 !important;
        }

        .modal-backdrop {
            width: 100% !important;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            padding: 1.5rem;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-brand {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 2rem;
        }

        .sidebar-brand i {
            color: var(--primary-color);
        }

        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-nav .nav-label {
            color: #64748b;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1rem 0 0.5rem;
        }

        .sidebar-nav .nav-link {
            color: #94a3b8;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            transition: all 0.2s;
            margin-bottom: 0.25rem;
        }

        .sidebar-nav .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-nav .nav-link.active {
            color: white;
            background: var(--primary-color);
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        /* Top Navbar */
        .top-navbar {
            background: white;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .page-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }

        /* Content Area */
        .content-wrapper {
            padding: 1.5rem;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        /* Stats Card */
        .stats-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stats-card .icon {
            width: 48px;
            height: 48px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .stats-card .value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
        }

        .stats-card .label {
            color: #64748b;
            font-size: 0.875rem;
        }

        /* Buttons */
        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        /* Badge */
        .badge-unprocessed {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-processed {
            background: #d1fae5;
            color: #065f46;
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <i class="bi bi-bag-heart-fill"></i>
            LaraFashion
        </a>

        <ul class="sidebar-nav">
            <li class="nav-label">Dashboard</li>
            <li>
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i>
                    Tổng quan
                </a>
            </li>

            <li class="nav-label">Quản lý</li>
            <li>
                <a href="{{ route('admin.products.index') }}"
                    class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i>
                    Sản phẩm
                </a>
            </li>
            <li>
                <a href="{{ route('admin.categories.index') }}"
                    class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="bi bi-folder"></i>
                    Danh mục
                </a>
            </li>
            <li>
                <a href="{{ route('admin.brands.index') }}"
                    class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                    <i class="bi bi-bookmark-star"></i>
                    Thương hiệu
                </a>
            </li>
            <li>
                <a href="{{ route('admin.orders.index') }}"
                    class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i>
                    Đơn hàng
                </a>
            </li>

            <li class="nav-label">Quản lý kho</li>
            <li>
                <a href="{{ route('admin.suppliers.index') }}"
                    class="nav-link {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
                    <i class="bi bi-truck"></i>
                    Nhà cung cấp
                </a>
            </li>
            <li>
                <a href="{{ route('admin.stock-imports.index') }}"
                    class="nav-link {{ request()->routeIs('admin.stock-imports.*') ? 'active' : '' }}">
                    <i class="bi bi-box-arrow-in-down"></i>
                    Phiếu nhập hàng
                </a>
            </li>

            <li class="nav-label">Marketing</li>
            <li>
                <a href="{{ route('admin.banners.index') }}"
                    class="nav-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                    <i class="bi bi-images"></i>
                    Banner
                </a>
            </li>
            <li>
                <a href="{{ route('admin.vouchers.index') }}"
                    class="nav-link {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}">
                    <i class="bi bi-ticket-perforated"></i>
                    Voucher
                </a>
            </li>

            <li class="nav-label">Hệ thống</li>
            <li>
                <a href="{{ route('admin.users.index') }}"
                    class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    Người dùng
                </a>
            </li>
            <li>
                <form action="{{ route('admin.logout') }}" method="POST" class="d-inline w-100">
                    @csrf
                    <button type="submit" class="nav-link text-danger bg-transparent border-0 w-100 text-start">
                        <i class="bi bi-box-arrow-left"></i>
                        Đăng xuất
                    </button>
                </form>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navbar -->
        <nav class="top-navbar">
            <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>

            <div class="d-flex align-items-center gap-3">
                <span class="text-muted">{{ auth('admin')->user()->name ?? 'Admin' }}</span>
            </div>
        </nav>

        <!-- Content -->
        <div class="content-wrapper">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


    <!-- Toast Notifications -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div id="adminToast" class="toast align-items-center border-0" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="toast-icon bi"></i>
                    <span class="toast-message"></span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <script>
        function showToast(message, type = 'success') {
            const toastEl = document.getElementById('adminToast');
            const toastIcon = toastEl.querySelector('.toast-icon');
            const toastMessage = toastEl.querySelector('.toast-message');

            toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'bg-info', 'text-white');
            toastIcon.classList.remove('bi-check-circle-fill', 'bi-x-circle-fill', 'bi-exclamation-triangle-fill', 'bi-info-circle-fill');

            const config = {
                success: { bg: 'bg-success', icon: 'bi-check-circle-fill' },
                error: { bg: 'bg-danger', icon: 'bi-x-circle-fill' },
                warning: { bg: 'bg-warning', icon: 'bi-exclamation-triangle-fill' },
                info: { bg: 'bg-info', icon: 'bi-info-circle-fill' }
            };

            const { bg, icon } = config[type] || config.success;
            toastEl.classList.add(bg, 'text-white');
            toastIcon.classList.add(icon);
            toastMessage.textContent = message;

            const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
            toast.show();
        }
    </script>

    @stack('scripts')
</body>

</html>
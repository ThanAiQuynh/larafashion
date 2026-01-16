<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Thời trang') - LaraFashion</title>

    <!-- SEO Meta -->
    <meta name="description" content="@yield('description', 'LaraFashion - Thời trang cao cấp, phong cách hiện đại')">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <style>
        :root {
            --primary-color: #6366f1;
            --primary-dark: #4f46e5;
            --secondary-color: #f59e0b;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #f8fafc;
        }

        /* Navbar */
        .navbar {
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
        }

        .nav-link {
            color: #475569;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
        }

        .nav-link:hover {
            color: var(--primary-color);
        }

        /* Search */
        .search-form {
            max-width: 400px;
        }

        .search-form .form-control {
            border-radius: 2rem 0 0 2rem;
            border-right: none;
        }

        .search-form .btn {
            border-radius: 0 2rem 2rem 0;
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Cart Badge */
        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            font-size: 0.65rem;
            padding: 0.25rem 0.4rem;
        }

        /* Product Cards */
        .product-card {
            border: none;
            border-radius: 1rem;
            overflow: hidden;
            transition: all 0.3s ease;
            background: white;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .product-card .card-img-top {
            height: 250px;
            object-fit: cover;
            flex-shrink: 0;
        }

        .product-card .card-body {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .product-card .card-title {
            min-height: 3rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-card .btn-add-cart {
            margin-top: auto;
        }

        .product-card .badge-sale {
            position: absolute;
            top: 1rem;
            left: 1rem;
            background: #ef4444;
        }

        .product-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .product-price-old {
            text-decoration: line-through;
            color: #94a3b8;
            font-size: 0.9rem;
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

        .btn-add-cart {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 0.5rem;
        }

        /* Footer */
        .footer {
            background: #1e293b;
            color: #94a3b8;
            padding: 3rem 0;
        }

        .footer h5 {
            color: white;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .footer a {
            color: #94a3b8;
            text-decoration: none;
        }

        .footer a:hover {
            color: white;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 4rem 0;
            color: white;
        }

        .hero-section h1 {
            font-size: 3rem;
            font-weight: 700;
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bi bi-bag-heart-fill me-2"></i>LaraFashion
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @isset($navCategories)
                        @foreach($navCategories as $parentCategory)
                            @if($parentCategory->children->count() > 0)
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        {{ $parentCategory->name }}
                                    </a>
                                    <ul class="dropdown-menu shadow border-0">
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('products.index', ['category' => $parentCategory->slug]) }}">
                                                <strong>Tất cả {{ $parentCategory->name }}</strong>
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        @foreach($parentCategory->children as $childCategory)
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('products.index', ['category' => $childCategory->slug]) }}">
                                                    {{ $childCategory->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ route('products.index', ['category' => $parentCategory->slug]) }}">{{ $parentCategory->name }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endisset
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="{{ route('products.sale') }}">Sale</a>
                    </li>
                </ul>

                <!-- Search -->
                <form action="{{ route('products.index') }}" method="GET" class="d-flex search-form me-3">
                    <input class="form-control" type="search" name="search" placeholder="Tìm kiếm sản phẩm..."
                        value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </form>

                <!-- User Actions -->
                <ul class="navbar-nav">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="bi bi-person-plus me-1"></i> Đăng ký
                            </a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle fs-5 me-1"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                <li><a class="dropdown-item" href="{{ route('account.index') }}"><i
                                            class="bi bi-person me-2"></i>Tài khoản</a></li>
                                <li><a class="dropdown-item" href="{{ route('account.orders') }}"><i
                                            class="bi bi-bag me-2"></i>Đơn hàng</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"><i
                                                class="bi bi-box-arrow-right me-2"></i>Đăng xuất</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                    <li class="nav-item position-relative">
                        <a class="nav-link" href="{{ route('cart.index') }}">
                            <i class="bi bi-cart3 fs-5"></i>
                            @php $cartCount = count(session('cart', [])); @endphp
                            <span class="badge bg-danger cart-badge cart-count-badge"
                                style="{{ $cartCount == 0 ? 'display:none;' : '' }}">
                                {{ $cartCount }}
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5><i class="bi bi-bag-heart-fill me-2"></i>LaraFashion</h5>
                    <p>Thương hiệu thời trang hàng đầu Việt Nam với phong cách hiện đại, chất lượng cao cấp.</p>
                    <div class="d-flex gap-3">
                        <a href="#"><i class="bi bi-facebook fs-4"></i></a>
                        <a href="#"><i class="bi bi-instagram fs-4"></i></a>
                        <a href="#"><i class="bi bi-tiktok fs-4"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 mb-4">
                    <h5>Danh mục</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#">Áo</a></li>
                        <li class="mb-2"><a href="#">Quần</a></li>
                        <li class="mb-2"><a href="#">Váy</a></li>
                        <li class="mb-2"><a href="#">Phụ kiện</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4 mb-4">
                    <h5>Hỗ trợ</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#">Hướng dẫn mua hàng</a></li>
                        <li class="mb-2"><a href="#">Đổi trả hàng</a></li>
                        <li class="mb-2"><a href="#">Vận chuyển</a></li>
                        <li class="mb-2"><a href="#">Liên hệ</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4 mb-4">
                    <h5>Liên hệ</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-geo-alt me-2"></i>112 Ngõ 28/76 Đường Xuân La, Tây Hồ, Hà Nội.
                        </li>
                        <li class="mb-2"><i class="bi bi-telephone me-2"></i>1900 1234</li>
                        <li class="mb-2"><i class="bi bi-envelope me-2"></i>support@larafashion.vn</li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary">
            <div class="text-center">
                <small>© {{ date('Y') }} LaraFashion. All rights reserved.</small>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!-- Tudongchat Chatbot -->
    @php
        $chatbotConfig = \App\Models\ChatbotConfig::getConfig();
    @endphp

    <script>
        // Clear Tudongchat chat history on new session (when user returns to website)
        (function () {
            // Check if this is a new browser session
            if (!sessionStorage.getItem('chatSessionActive')) {
                // New session - clear old chat data
                // Remove Tudongchat related localStorage items
                Object.keys(localStorage).forEach(key => {
                    if (key.includes('tudong') || key.includes('TuDong') || key.includes('chatbox')) {
                        localStorage.removeItem(key);
                    }
                });
                // Mark session as active
                sessionStorage.setItem('chatSessionActive', 'true');
            }
        })();
    </script>

    @if($chatbotConfig && $chatbotConfig->is_active && $chatbotConfig->script_code)
        {!! $chatbotConfig->script_code !!}
    @else
        <script src="https://app.tudongchat.com/js/chatbox.js"></script>
        <script>
            const tudong_chatbox = new TuDongChat('zWH57ukT6Yjgkmh7x9X1u')
            tudong_chatbox.initial()
        </script>
    @endif

    <!-- Toast Notifications -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div id="appToast" class="toast align-items-center border-0" role="alert" aria-live="assertive"
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
        // Toast notification function
        function showToast(message, type = 'success') {
            const toastEl = document.getElementById('appToast');
            const toastIcon = toastEl.querySelector('.toast-icon');
            const toastMessage = toastEl.querySelector('.toast-message');

            // Remove previous classes
            toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'bg-info', 'text-white');
            toastIcon.classList.remove('bi-check-circle-fill', 'bi-x-circle-fill', 'bi-exclamation-triangle-fill', 'bi-info-circle-fill');

            // Set type-specific styles
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
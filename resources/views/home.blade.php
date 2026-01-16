@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')
    <!-- Hero Banner Carousel -->
    @php
        $banners = \App\Models\Banner::active()->ordered()->get();
    @endphp
    @if($banners->count() > 0)
        <section class="hero-banner">
            <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    @foreach($banners as $index => $banner)
                        <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="{{ $index }}"
                            class="{{ $index === 0 ? 'active' : '' }}"></button>
                    @endforeach
                </div>
                <div class="carousel-inner">
                    @foreach($banners as $index => $banner)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            @if($banner->link_url)
                                <a href="{{ $banner->link_url }}">
                                    <img src="{{ $banner->image_url }}" class="d-block w-100" alt="{{ $banner->title }}"
                                        style="max-height: 500px; object-fit: cover;">
                                </a>
                            @else
                                <img src="{{ $banner->image_url }}" class="d-block w-100" alt="{{ $banner->title }}"
                                    style="max-height: 500px; object-fit: cover;">
                            @endif
                        </div>
                    @endforeach
                </div>
                @if($banners->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                @endif
            </div>
        </section>
    @else
        <!-- Fallback Hero Section -->
        <section class="hero-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h1 class="mb-4">Thời trang<br>Phong cách bạn</h1>
                        <p class="lead mb-4">Khám phá bộ sưu tập mới nhất với những thiết kế độc đáo, chất liệu cao cấp.</p>
                        <a href="#products" class="btn btn-light btn-lg px-4">
                            Khám phá ngay <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Category Slider Section -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 1px;">Danh mục sản phẩm</h2>
                <div class="d-flex gap-2">
                    <button class="btn btn-link text-dark p-0 me-2 btn-category-prev">
                        <i class="bi bi-arrow-left fs-4"></i>
                    </button>
                    <button class="btn btn-link text-dark p-0 btn-category-next">
                        <i class="bi bi-arrow-right fs-4"></i>
                    </button>
                </div>
            </div>

            <div class="swiper category-swiper overflow-hidden">
                <div class="swiper-wrapper">
                    @foreach($categories as $category)
                        <div class="swiper-slide h-auto">
                            <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                                class="text-decoration-none">
                                <div class="category-card rounded-0 overflow-hidden position-relative" style="height: 400px;">
                                    <img src="{{ $category->image_url ?: 'https://placehold.co/400x500/e2e8f0/64748b?text=' . urlencode($category->name) }}"
                                        class="w-100 h-100 object-fit-cover transition-transform" alt="{{ $category->name }}">

                                    <div class="position-absolute bottom-0 start-0 w-100 p-3 d-flex justify-content-between align-items-center"
                                        style="background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(5px);">
                                        <span class="fw-bold text-dark text-uppercase small">{{ $category->name }}</span>
                                        <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center"
                                            style="width: 32px; height: 32px;">
                                            <i class="bi bi-arrow-right text-dark"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    @push('styles')
        <style>
            .category-card:hover img {
                transform: scale(1.05);
            }

            .transition-transform {
                transition: transform 0.5s ease;
            }

            .category-card {
                cursor: pointer;
            }
        </style>
    @endpush


    <!-- Featured Products -->
    <section id="products" class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0">Sản phẩm nổi bật</h2>
                <div class="d-flex gap-2 align-items-center">
                    <a href="{{ route('products.index') }}" class="text-decoration-none me-3 d-none d-md-block">
                        Xem tất cả <i class="bi bi-arrow-right"></i>
                    </a>
                    <button class="btn btn-outline-primary btn-sm rounded-circle featured-prev">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button class="btn btn-outline-primary btn-sm rounded-circle featured-next">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>

            <!-- Swiper Container -->
            <div class="swiper featured-products-swiper pb-4">
                <div class="swiper-wrapper">
                    @forelse($featuredProducts as $product)
                        <div class="swiper-slide h-auto">
                            <div class="card product-card h-100">
                                <div class="position-relative">
                                    @if($product->getDiscountPercentage())
                                        <span class="badge badge-sale">-{{ $product->getDiscountPercentage() }}%</span>
                                    @endif
                                    <a href="{{ route('products.show', $product->slug) }}">
                                        <img src="{{ $product->thumbnail_url ?: 'https://placehold.co/400x500/e2e8f0/64748b?text=No+Image' }}"
                                            class="card-img-top" alt="{{ $product->name }}">
                                    </a>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted small mb-1">{{ $product->brand?->name }}</p>
                                    <h6 class="card-title mb-2">
                                        <a href="{{ route('products.show', $product->slug) }}"
                                            class="text-dark text-decoration-none">{{ Str::limit($product->name, 40) }}</a>
                                    </h6>
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <span class="product-price">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                                        @if($product->original_price)
                                            <span
                                                class="product-price-old">{{ number_format($product->original_price, 0, ',', '.') }}đ</span>
                                        @endif
                                    </div>
                                    <button class="btn btn-add-cart btn-primary w-100 btn-add-to-cart"
                                        data-id="{{ $product->id }}">
                                        <i class="bi bi-cart-plus me-1"></i> Thêm vào giỏ
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="swiper-slide text-center py-5">
                            <i class="bi bi-box-seam display-4 text-muted"></i>
                            <p class="mt-3 text-muted">Chưa có sản phẩm nào</p>
                        </div>
                    @endforelse
                </div>
                <!-- Pagination -->
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>

    <!-- Sale Products Section -->
    @if(isset($saleProducts) && $saleProducts->count() > 0)
        <section class="py-5" style="background: linear-gradient(135deg, #fff5f5 0%, #ffe8e8 100%);">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold mb-0 text-danger">
                            <i class="bi bi-lightning-fill me-2"></i>Sản phẩm khuyến mãi
                        </h2>
                        <p class="text-muted mb-0">Giảm giá sốc - Số lượng có hạn!</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if($saleProducts->count() > 4)
                            <button class="btn btn-outline-danger btn-sm rounded-circle sale-prev">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-sm rounded-circle sale-next">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        @endif
                        <a href="{{ route('products.sale') }}" class="btn btn-danger btn-sm rounded-pill px-4 ms-2">
                            Xem tất cả <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>

                @if($saleProducts->count() > 4)
                    <!-- Swiper Slider for 5+ products -->
                    <div class="swiper sale-products-swiper">
                        <div class="swiper-wrapper">
                            @foreach($saleProducts as $product)
                                <div class="swiper-slide h-auto">
                                    <div class="card product-card h-100 border-0 shadow-sm">
                                        <div class="position-relative">
                                            @if($product->getDiscountPercentage())
                                                <span class="badge bg-danger position-absolute top-0 start-0 m-2 px-2 py-1"
                                                    style="z-index: 2;">
                                                    -{{ $product->getDiscountPercentage() }}%
                                                </span>
                                            @endif
                                            <a href="{{ route('products.show', $product->slug) }}">
                                                <img src="{{ $product->thumbnail_url ?: 'https://placehold.co/400x500/e2e8f0/64748b?text=No+Image' }}"
                                                    class="card-img-top" alt="{{ $product->name }}">
                                            </a>
                                        </div>
                                        <div class="card-body">
                                            <p class="text-muted small mb-1">{{ $product->brand?->name }}</p>
                                            <h6 class="card-title mb-2">
                                                <a href="{{ route('products.show', $product->slug) }}"
                                                    class="text-dark text-decoration-none">
                                                    {{ Str::limit($product->name, 40) }}
                                                </a>
                                            </h6>
                                            <div class="d-flex align-items-center gap-2 mb-3">
                                                <span
                                                    class="product-price text-danger fw-bold">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                                                <span class="product-price-old text-muted text-decoration-line-through small">
                                                    {{ number_format($product->original_price, 0, ',', '.') }}đ
                                                </span>
                                            </div>
                                            <button class="btn btn-danger w-100 btn-add-to-cart" data-id="{{ $product->id }}">
                                                <i class="bi bi-cart-plus me-1"></i> Thêm vào giỏ
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <!-- Grid for 4 or fewer products -->
                    <div class="row g-4">
                        @foreach($saleProducts as $product)
                            <div class="col-lg-3 col-md-4 col-6">
                                <div class="card product-card h-100 border-0 shadow-sm">
                                    <div class="position-relative">
                                        @if($product->getDiscountPercentage())
                                            <span class="badge bg-danger position-absolute top-0 start-0 m-2 px-2 py-1" style="z-index: 2;">
                                                -{{ $product->getDiscountPercentage() }}%
                                            </span>
                                        @endif
                                        <a href="{{ route('products.show', $product->slug) }}">
                                            <img src="{{ $product->thumbnail_url ?: 'https://placehold.co/400x500/e2e8f0/64748b?text=No+Image' }}"
                                                class="card-img-top" alt="{{ $product->name }}">
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted small mb-1">{{ $product->brand?->name }}</p>
                                        <h6 class="card-title mb-2">
                                            <a href="{{ route('products.show', $product->slug) }}"
                                                class="text-dark text-decoration-none">
                                                {{ Str::limit($product->name, 40) }}
                                            </a>
                                        </h6>
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <span
                                                class="product-price text-danger fw-bold">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                                            <span class="product-price-old text-muted text-decoration-line-through small">
                                                {{ number_format($product->original_price, 0, ',', '.') }}đ
                                            </span>
                                        </div>
                                        <button class="btn btn-danger w-100 btn-add-to-cart" data-id="{{ $product->id }}">
                                            <i class="bi bi-cart-plus me-1"></i> Thêm vào giỏ
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif


    <!-- All Products -->
    <section class="py-5 bg-white">
        <div class="container">
            <h2 class="fw-bold mb-4">Tất cả sản phẩm</h2>

            <div class="row g-4">
                @forelse($products as $product)
                    <div class="col-lg-3 col-md-4 col-6">
                        <div class="card product-card h-100">
                            <div class="position-relative">
                                @if($product->getDiscountPercentage())
                                    <span class="badge badge-sale">-{{ $product->getDiscountPercentage() }}%</span>
                                @endif
                                <img src="{{ $product->thumbnail_url ?: 'https://placehold.co/400x500/e2e8f0/64748b?text=No+Image' }}"
                                    class="card-img-top" alt="{{ $product->name }}">
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-1">{{ $product->brand?->name }}</p>
                                <h6 class="card-title mb-2">{{ Str::limit($product->name, 40) }}</h6>
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="product-price">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                                    @if($product->original_price)
                                        <span
                                            class="product-price-old">{{ number_format($product->original_price, 0, ',', '.') }}đ</span>
                                    @endif
                                </div>
                                <button class="btn btn-add-cart btn-primary w-100 btn-add-to-cart" data-id="{{ $product->id }}">
                                    <i class="bi bi-cart-plus me-1"></i> Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="bi bi-box-seam display-4 text-muted"></i>
                            <p class="mt-3 text-muted">Chưa có sản phẩm nào</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if(isset($totalProductCount) && $totalProductCount > 12)
                <div class="text-center mt-4">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-lg px-5 rounded-pill">
                        Xem tất cả sản phẩm <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            @endif
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%);">
        <div class="container text-center text-white">
            <h2 class="fw-bold mb-3">Cần tư vấn?</h2>
            <p class="lead mb-4">Chat với AI của chúng tôi để được hỗ trợ 24/7!</p>
            <p class="text-white-50">
                <i class="bi bi-chat-dots me-2"></i>
                Nhấn vào biểu tượng chat ở góc phải màn hình
            </p>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Featured Products Swiper
            const featuredSwiper = new Swiper('.featured-products-swiper', {
                slidesPerView: 2,
                spaceBetween: 20,
                loop: true,
                autoplay: {
                    delay: 1000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.featured-next',
                    prevEl: '.featured-prev',
                },
                breakpoints: {
                    // when window width is >= 768px
                    768: {
                        slidesPerView: 3,
                    },
                    // when window width is >= 1024px
                    1024: {
                        slidesPerView: 4,
                    }
                }
            });

            // Initialize Category Swiper
            const categorySwiper = new Swiper('.category-swiper', {
                slidesPerView: 2,
                spaceBetween: 20,
                loop: false,
                navigation: {
                    nextEl: '.btn-category-next',
                    prevEl: '.btn-category-prev',
                },
                breakpoints: {
                    768: { slidesPerView: 3 },
                    1024: { slidesPerView: 4 }
                }
            });

            // Initialize Sale Products Swiper
            const saleSwiper = new Swiper('.sale-products-swiper', {
                slidesPerView: 2,
                spaceBetween: 16,
                loop: false,
                navigation: {
                    nextEl: '.sale-next',
                    prevEl: '.sale-prev',
                },
                breakpoints: {
                    768: { slidesPerView: 3 },
                    1024: { slidesPerView: 4 }
                }
            });

            document.querySelectorAll('.btn-add-to-cart').forEach(button => {
                button.addEventListener('click', function () {
                    const productId = this.getAttribute('data-id');
                    const quantity = 1; // Default for list view

                    fetch('{{ route("cart.add") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ product_id: productId, quantity: quantity })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update cart count badge
                                const badge = document.querySelector('.cart-count-badge');
                                if (badge) {
                                    badge.textContent = data.cart_count;
                                    badge.style.display = 'block';
                                }

                                // Optional: Show a toast or notification
                                showToast(data.message, 'success');
                            }
                        });
                });
            });
        });
    </script>
@endpush
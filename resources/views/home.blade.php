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
    
    <!-- Featured Products -->
    <section id="products" class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0">Sản phẩm nổi bật</h2>
                <a href="{{ route('products.index') }}" class="text-decoration-none">
                    Xem tất cả <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            
            <div class="row g-4">
                @forelse($featuredProducts as $product)
                    <div class="col-lg-3 col-md-4 col-6">
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
                                    <a href="{{ route('products.show', $product->slug) }}" class="text-dark text-decoration-none">{{ Str::limit($product->name, 40) }}</a>
                                </h6>
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="product-price">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                                    @if($product->original_price)
                                        <span class="product-price-old">{{ number_format($product->original_price, 0, ',', '.') }}đ</span>
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
        </div>
    </section>
    
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
                                        <span class="product-price-old">{{ number_format($product->original_price, 0, ',', '.') }}đ</span>
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
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
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
                        alert(data.message);
                    }
                });
            });
        });
    });
</script>
@endpush

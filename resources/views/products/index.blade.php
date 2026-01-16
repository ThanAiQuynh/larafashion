@extends('layouts.app')

@section('title', 'Tất cả sản phẩm - LaraFashion')

@section('content')
    <div class="container py-4">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Sản phẩm</li>
            </ol>
        </nav>

        <div class="row g-4">
            <!-- Sidebar Filters -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Danh mục</h5>
                        <div class="list-group list-group-flush">
                            <a href="{{ route('products.index') }}"
                                class="list-group-item list-group-item-action border-0 px-0 {{ !request('category') ? 'text-primary fw-bold' : '' }}">
                                Tất cả sản phẩm
                            </a>
                            @foreach($categories as $category)
                                @php
                                    $isParentActive = request('category') == $category->slug;
                                    $hasActiveChild = $category->children->contains('slug', request('category'));
                                    $shouldExpand = $isParentActive || $hasActiveChild;
                                @endphp
                                <div class="mt-2">
                                    <a href="#collapse-{{ $category->id }}"
                                        class="fw-medium text-muted small text-uppercase text-decoration-none d-flex justify-content-between align-items-center {{ $isParentActive ? 'text-primary' : '' }}"
                                        data-bs-toggle="collapse" aria-expanded="{{ $shouldExpand ? 'true' : 'false' }}">
                                        {{ $category->name }}
                                        <i class="bi bi-chevron-down small"></i>
                                    </a>
                                    <div class="collapse {{ $shouldExpand ? 'show' : '' }}" id="collapse-{{ $category->id }}">
                                        <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                                            class="list-group-item list-group-item-action border-0 px-0 ps-3 small {{ $isParentActive ? 'text-primary fw-bold' : '' }}">
                                            Tất cả {{ $category->name }}
                                        </a>
                                        @foreach($category->children as $child)
                                            <a href="{{ route('products.index', ['category' => $child->slug]) }}"
                                                class="list-group-item list-group-item-action border-0 px-0 ps-3 {{ request('category') == $child->slug ? 'text-primary fw-bold' : '' }}">
                                                {{ $child->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Thương hiệu</h5>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($brands as $brand)
                                <a href="{{ route('products.index', ['brand' => $brand->slug]) }}"
                                    class="btn btn-sm {{ request('brand') == $brand->slug ? 'btn-primary' : 'btn-outline-secondary' }}">
                                    {{ $brand->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3 d-flex justify-content-between align-items-center" data-bs-toggle="collapse"
                            href="#price-collapse" role="button" aria-expanded="true">
                            Khoảng giá
                            <i class="bi bi-dash"></i>
                        </h5>
                        <div class="collapse show" id="price-collapse">
                            <form action="{{ route('products.index') }}" method="GET" id="price-filter-form">
                                @foreach(request()->except(['price_min', 'price_max', 'page']) as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                                <div class="d-flex justify-content-between mb-2">
                                    <input type="text" class="form-control form-control-sm text-center"
                                        id="price-min-display" style="width: 45%;" readonly>
                                    <input type="text" class="form-control form-control-sm text-center"
                                        id="price-max-display" style="width: 45%;" readonly>
                                </div>
                                <div id="price-slider" class="mb-3"></div>
                                <input type="hidden" name="price_min" id="price-min-input"
                                    value="{{ request('price_min', 0) }}">
                                <input type="hidden" name="price_max" id="price-max-input"
                                    value="{{ request('price_max', $maxPrice) }}">
                                <button type="submit" class="btn btn-primary btn-sm w-100">Áp dụng</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold mb-0">Sản phẩm</h2>
                        <p class="text-muted mb-0">Hiển thị {{ $products->count() }} trong tổng số {{ $products->total() }}
                            sản phẩm</p>
                    </div>
                    <div class="d-flex gap-2">
                        <select class="form-select border-0 shadow-sm" style="width: auto;"
                            onchange="location = this.value;">
                            <option
                                value="{{ route('products.index', ['sort' => 'newest'] + request()->except('sort', 'page')) }}"
                                {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                            <option
                                value="{{ route('products.index', ['sort' => 'price_asc'] + request()->except('sort', 'page')) }}"
                                {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá: Thấp đến Cao</option>
                            <option
                                value="{{ route('products.index', ['sort' => 'price_desc'] + request()->except('sort', 'page')) }}"
                                {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá: Cao đến Thấp</option>
                        </select>
                    </div>
                </div>

                <div class="row g-4">
                    @forelse($products as $product)
                        <div class="col-md-4 col-sm-6">
                            <div class="card h-100 border-0 shadow-sm product-card">
                                <div class="position-relative overflow-hidden">
                                    <img src="{{ $product->thumbnail_url ?: 'https://placehold.co/400x500?text=No+Image' }}"
                                        class="card-img-top" alt="{{ $product->name }}"
                                        style="height: 300px; object-fit: cover;">
                                    @if($product->original_price)
                                        <span
                                            class="position-absolute top-0 start-0 bg-danger text-white px-3 py-1 m-2 rounded-pill small">
                                            -{{ round((($product->original_price - $product->price) / $product->original_price) * 100) }}%
                                        </span>
                                    @endif
                                    <div
                                        class="product-actions position-absolute bottom-0 start-0 w-100 p-3 bg-white bg-opacity-75 translate-y-100 transition">
                                        <button class="btn btn-primary w-100 rounded-pill mb-2 btn-add-to-cart"
                                            data-id="{{ $product->id }}">Thêm vào giỏ</button>
                                        <a href="{{ route('products.show', $product->slug) }}"
                                            class="btn btn-outline-dark w-100 rounded-pill">Chi tiết</a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted small mb-1">{{ $product->brand?->name }}</p>
                                    <h6 class="fw-bold mb-2">
                                        <a href="{{ route('products.show', $product->slug) }}"
                                            class="text-dark text-decoration-none">{{ $product->name }}</a>
                                    </h6>
                                    <div class="d-flex align-items-center gap-2">
                                        <span
                                            class="text-primary fw-bold">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                                        @if($product->original_price)
                                            <span
                                                class="text-muted text-decoration-line-through small">{{ number_format($product->original_price, 0, ',', '.') }}đ</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <i class="bi bi-search display-1 text-muted"></i>
                            <p class="mt-3 text-muted">Không tìm thấy sản phẩm nào phù hợp.</p>
                            <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">Xem tất cả sản phẩm</a>
                        </div>
                    @endforelse
                </div>

                <div class="mt-5 d-flex justify-content-center">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>

    <style>
        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .product-card:hover .product-actions {
            transform: translateY(0);
        }

        .transition {
            transition: all 0.3s ease;
        }

        .translate-y-100 {
            transform: translateY(100%);
        }

        /* noUiSlider Custom Styles */
        #price-slider {
            height: 6px;
            margin: 15px 0;
        }

        #price-slider .noUi-connect {
            background: #333;
        }

        #price-slider .noUi-handle {
            height: 20px;
            width: 20px;
            top: -8px;
            right: -10px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid #333;
            box-shadow: none;
            cursor: pointer;
        }

        #price-slider .noUi-handle:before,
        #price-slider .noUi-handle:after {
            display: none;
        }
    </style>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.css" rel="stylesheet">
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.btn-add-to-cart').forEach(button => {
                button.addEventListener('click', function () {
                    const productId = this.getAttribute('data-id');
                    const quantity = 1;

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
                                const badge = document.querySelector('.cart-count-badge');
                                if (badge) {
                                    badge.textContent = data.cart_count;
                                    badge.style.display = 'block';
                                }
                                showToast(data.message, 'success');
                            }
                        });
                });
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const slider = document.getElementById('price-slider');
            const minInput = document.getElementById('price-min-input');
            const maxInput = document.getElementById('price-max-input');
            const minDisplay = document.getElementById('price-min-display');
            const maxDisplay = document.getElementById('price-max-display');

            const maxPrice = {{ $maxPrice ?? 5000000 }};
            const currentMin = {{ request('price_min', 0) }};
            const currentMax = {{ request('price_max', $maxPrice ?? 5000000) }};

            function formatPrice(value) {
                return new Intl.NumberFormat('vi-VN').format(Math.round(value)) + 'đ';
            }

            noUiSlider.create(slider, {
                start: [currentMin, currentMax],
                connect: true,
                range: {
                    'min': 0,
                    'max': maxPrice
                },
                step: 50000
            });

            slider.noUiSlider.on('update', function (values, handle) {
                const min = Math.round(values[0]);
                const max = Math.round(values[1]);
                minDisplay.value = formatPrice(min);
                maxDisplay.value = formatPrice(max);
                minInput.value = min;
                maxInput.value = max;
            });
        });
    </script>
@endpush
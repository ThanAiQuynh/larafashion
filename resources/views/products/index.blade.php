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
                        <a href="{{ route('products.index') }}" class="list-group-item list-group-item-action border-0 px-0 {{ !request('category') ? 'text-primary fw-bold' : '' }}">
                            Tất cả sản phẩm
                        </a>
                        @foreach($categories as $category)
                            <div class="mt-2">
                                <span class="fw-medium text-muted small text-uppercase">{{ $category->name }}</span>
                                @foreach($category->children as $child)
                                    <a href="{{ route('products.index', ['category' => $child->slug] + request()->except('category', 'page')) }}" 
                                       class="list-group-item list-group-item-action border-0 px-0 ps-3 {{ request('category') == $child->slug ? 'text-primary fw-bold' : '' }}">
                                        {{ $child->name }}
                                    </a>
                                @endforeach
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
                            <a href="{{ route('products.index', ['brand' => $brand->slug] + request()->except('brand', 'page')) }}" 
                               class="btn btn-sm {{ request('brand') == $brand->slug ? 'btn-primary' : 'btn-outline-secondary' }}">
                                {{ $brand->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-0">Sản phẩm</h2>
                    <p class="text-muted mb-0">Hiển thị {{ $products->count() }} trong tổng số {{ $products->total() }} sản phẩm</p>
                </div>
                <div class="d-flex gap-2">
                    <select class="form-select border-0 shadow-sm" style="width: auto;" onchange="location = this.value;">
                        <option value="{{ route('products.index', ['sort' => 'newest'] + request()->except('sort', 'page')) }}" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="{{ route('products.index', ['sort' => 'price_asc'] + request()->except('sort', 'page')) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá: Thấp đến Cao</option>
                        <option value="{{ route('products.index', ['sort' => 'price_desc'] + request()->except('sort', 'page')) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá: Cao đến Thấp</option>
                    </select>
                </div>
            </div>

            <div class="row g-4">
                @forelse($products as $product)
                    <div class="col-md-4 col-sm-6">
                        <div class="card h-100 border-0 shadow-sm product-card">
                            <div class="position-relative overflow-hidden">
                                <img src="{{ $product->thumbnail_url ?: 'https://placehold.co/400x500?text=No+Image' }}" 
                                     class="card-img-top" alt="{{ $product->name }}" style="height: 300px; object-fit: cover;">
                                @if($product->original_price)
                                    <span class="position-absolute top-0 start-0 bg-danger text-white px-3 py-1 m-2 rounded-pill small">
                                        -{{ round((($product->original_price - $product->price) / $product->original_price) * 100) }}%
                                    </span>
                                @endif
                                <div class="product-actions position-absolute bottom-0 start-0 w-100 p-3 bg-white bg-opacity-75 translate-y-100 transition">
                                    <button class="btn btn-primary w-100 rounded-pill mb-2 btn-add-to-cart" data-id="{{ $product->id }}">Thêm vào giỏ</button>
                                    <a href="{{ route('products.show', $product->slug) }}" class="btn btn-outline-dark w-100 rounded-pill">Chi tiết</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-1">{{ $product->brand?->name }}</p>
                                <h6 class="fw-bold mb-2">
                                    <a href="{{ route('products.show', $product->slug) }}" class="text-dark text-decoration-none">{{ $product->name }}</a>
                                </h6>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-primary fw-bold">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                                    @if($product->original_price)
                                        <span class="text-muted text-decoration-line-through small">{{ number_format($product->original_price, 0, ',', '.') }}đ</span>
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
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
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
</style>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
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
@endpush

@extends('layouts.app')

@section('title', $product->name . ' - LaraFashion')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Sản phẩm</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Product Images -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm overflow-hidden rounded-4">
                <img src="{{ $product->thumbnail_url ?: 'https://placehold.co/600x800?text=No+Image' }}" 
                     class="img-fluid" alt="{{ $product->name }}">
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-lg-6">
            <div class="ps-lg-4">
                <p class="text-primary text-uppercase fw-bold mb-2">{{ $product->brand?->name }}</p>
                <h1 class="fw-bold mb-3 display-5">{{ $product->name }}</h1>
                
                @php
                    $approvedReviews = $product->reviews->where('is_approved', true);
                    $avgRating = $approvedReviews->avg('rating') ?? 0;
                    $reviewCount = $approvedReviews->count();
                @endphp
                <div class="d-flex align-items-center mb-4">
                    <div class="text-warning me-2">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($avgRating))
                                <i class="bi bi-star-fill"></i>
                            @elseif($i - 0.5 <= $avgRating)
                                <i class="bi bi-star-half"></i>
                            @else
                                <i class="bi bi-star"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="text-muted small">({{ $reviewCount }} đánh giá)</span>
                </div>

                <div class="d-flex align-items-center gap-3 mb-4">
                    <h2 class="text-primary fw-bold mb-0 display-6">{{ number_format($product->price, 0, ',', '.') }}đ</h2>
                    @if($product->original_price)
                        <span class="text-muted text-decoration-line-through fs-5">{{ number_format($product->original_price, 0, ',', '.') }}đ</span>
                        <span class="badge bg-danger rounded-pill">-{{ round((($product->original_price - $product->price) / $product->original_price) * 100) }}%</span>
                    @endif
                </div>

                <div class="mb-4 text-muted border-bottom pb-4">
                    {{ $product->description }}
                </div>

                <div class="mb-4">
                    <div class="d-flex align-items-center gap-4">
                        <div class="input-group" style="width: 140px;">
                            <button class="btn btn-outline-secondary" type="button" onclick="this.parentNode.querySelector('input').stepDown()">-</button>
                            <input type="number" id="buy-quantity" class="form-control text-center" value="1" min="1" max="{{ $product->stock_quantity }}">
                            <button class="btn btn-outline-secondary" type="button" onclick="this.parentNode.querySelector('input').stepUp()">+</button>
                        </div>
                        <p class="mb-0 text-muted small">Còn lại: <strong>{{ $product->stock_quantity }}</strong> sản phẩm</p>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex">
                    <button class="btn btn-primary btn-lg rounded-pill px-5 flex-grow-1 btn-add-to-cart" data-id="{{ $product->id }}">
                        <i class="bi bi-cart-plus me-2"></i> Thêm vào giỏ
                    </button>
                    <button class="btn btn-outline-dark btn-lg rounded-pill px-4">
                        <i class="bi bi-heart"></i>
                    </button>
                </div>

                <div class="mt-5 p-4 bg-light rounded-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <i class="bi bi-truck fs-3 text-primary"></i>
                        <div>
                            <h6 class="fw-bold mb-0">Miễn phí giao hàng</h6>
                            <p class="text-muted small mb-0">Cho đơn hàng từ 500.000đ trở lên</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-arrow-left-right fs-3 text-primary"></i>
                        <div>
                            <h6 class="fw-bold mb-0">Đổi trả dễ dàng</h6>
                            <p class="text-muted small mb-0">Trong vòng 30 ngày kể từ khi nhận hàng</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="mt-5 pt-5 border-top">
        <div class="row">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-4">Đánh giá sản phẩm ({{ $reviewCount }})</h3>
                
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <!-- Review Form -->
                @auth
                    @php
                        $userReview = $product->reviews->where('user_id', auth()->id())->first();
                    @endphp
                    @if(!$userReview)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3">Viết đánh giá của bạn</h5>
                            <form action="{{ route('reviews.store', $product) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Đánh giá sao <span class="text-danger">*</span></label>
                                    <div class="star-rating">
                                        @for($i = 5; $i >= 1; $i--)
                                            <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" required>
                                            <label for="star{{ $i }}"><i class="bi bi-star-fill"></i></label>
                                        @endfor
                                    </div>
                                    @error('rating')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="comment" class="form-label">Nhận xét</label>
                                    <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send me-1"></i> Gửi đánh giá
                                </button>
                            </form>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle me-2"></i>Bạn đã đánh giá sản phẩm này.
                    </div>
                    @endif
                @else
                    <div class="alert alert-light border mb-4">
                        <i class="bi bi-person-circle me-2"></i>
                        <a href="{{ route('login') }}">Đăng nhập</a> để viết đánh giá.
                    </div>
                @endauth

                <!-- Reviews List -->
                @forelse($approvedReviews as $review)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="fw-bold">{{ $review->user->name }}</span>
                                    <span class="text-muted small">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-warning mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            @if(auth()->id() === $review->user_id)
                            <form action="{{ route('reviews.destroy', $review) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa đánh giá này?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                        @if($review->comment)
                            <p class="mb-0">{{ $review->comment }}</p>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-chat-square-text display-4"></i>
                    <p class="mt-3">Chưa có đánh giá nào cho sản phẩm này.</p>
                </div>
                @endforelse
            </div>
            
            <!-- Rating Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h1 class="display-3 fw-bold text-primary">{{ number_format($avgRating, 1) }}</h1>
                        <div class="text-warning mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($avgRating))
                                    <i class="bi bi-star-fill fs-5"></i>
                                @elseif($i - 0.5 <= $avgRating)
                                    <i class="bi bi-star-half fs-5"></i>
                                @else
                                    <i class="bi bi-star fs-5"></i>
                                @endif
                            @endfor
                        </div>
                        <p class="text-muted">{{ $reviewCount }} đánh giá</p>
                        
                        <!-- Rating breakdown -->
                        @for($star = 5; $star >= 1; $star--)
                            @php
                                $count = $approvedReviews->where('rating', $star)->count();
                                $percentage = $reviewCount > 0 ? ($count / $reviewCount) * 100 : 0;
                            @endphp
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="text-nowrap small">{{ $star }} <i class="bi bi-star-fill text-warning"></i></span>
                                <div class="progress flex-grow-1" style="height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="text-muted small" style="width: 30px;">{{ $count }}</span>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div class="mt-5 pt-5">
        <h3 class="fw-bold mb-4">Sản phẩm liên quan</h3>
        <div class="row g-4">
            @foreach($relatedProducts as $related)
            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm product-card">
                    <div class="position-relative overflow-hidden">
                        <a href="{{ route('products.show', $related->slug) }}">
                            <img src="{{ $related->thumbnail_url ?: 'https://placehold.co/400x500?text=No+Image' }}" 
                                 class="card-img-top" alt="{{ $related->name }}" style="height: 250px; object-fit: cover;">
                        </a>
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold mb-1">
                            <a href="{{ route('products.show', $related->slug) }}" class="text-dark text-decoration-none">{{ $related->name }}</a>
                        </h6>
                        <span class="text-primary fw-bold">{{ number_format($related->price, 0, ',', '.') }}đ</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.star-rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}
.star-rating input {
    display: none;
}
.star-rating label {
    cursor: pointer;
    font-size: 1.5rem;
    color: #ddd;
    padding: 0 2px;
}
.star-rating label:hover,
.star-rating label:hover ~ label,
.star-rating input:checked ~ label {
    color: #ffc107;
}
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('.btn-add-to-cart').addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            const quantity = document.getElementById('buy-quantity').value;

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
                    alert(data.message);
                }
            });
        });
    });
</script>
@endpush

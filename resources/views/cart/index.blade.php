@extends('layouts.app')

@section('title', 'Giỏ hàng - LaraFashion')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4">Giỏ hàng của bạn</h2>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
        @if(count($cart) > 0)
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3">Sản phẩm</th>
                                    <th class="py-3">Giá</th>
                                    <th class="py-3" style="width: 150px;">Số lượng</th>
                                    <th class="py-3">Tổng</th>
                                    <th class="pe-4 py-3 text-end">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cart as $id => $details)
                                    <tr data-id="{{ $id }}">
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="{{ $details['thumbnail_url'] ?: 'https://placehold.co/60x60?text=No+Image' }}"
                                                    alt="{{ $details['name'] }}" class="rounded"
                                                    style="width: 60px; hieght: 60px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0 fw-bold">{{ $details['name'] }}</h6>
                                                    @if(!empty($details['size']) || !empty($details['color']))
                                                        <small class="text-muted">
                                                            @if(!empty($details['size']))
                                                                Size: {{ $details['size'] }}
                                                            @endif
                                                            @if(!empty($details['size']) && !empty($details['color']))
                                                                |
                                                            @endif
                                                            @if(!empty($details['color']))
                                                                Màu: {{ $details['color'] }}
                                                            @endif
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ number_format($details['price'], 0, ',', '.') }}đ</td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <button class="btn btn-outline-secondary btn-update" type="button"
                                                    data-action="decrease">-</button>
                                                <input type="number" class="form-control text-center quantity-input"
                                                    value="{{ $details['quantity'] }}" min="1">
                                                <button class="btn btn-outline-secondary btn-update" type="button"
                                                    data-action="increase">+</button>
                                            </div>
                                        </td>
                                        <td class="item-total-price fw-bold text-primary">
                                            {{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}đ</td>
                                        <td class="pe-4 text-end text-end">
                                            <button class="btn btn-link text-danger p-0 btn-remove">
                                                <i class="bi bi-trash fs-5"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-dark rounded-pill px-4">
                        <i class="bi bi-arrow-left me-2"></i> Tiếp tục mua sắm
                    </a>
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger rounded-pill px-4"
                            onclick="return confirm('Xóa toàn bộ giỏ hàng?')">Xóa giỏ hàng</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-4">Tóm tắt đơn hàng</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tạm tính</span>
                        <span class="cart-total-price fw-bold">{{ number_format($total, 0, ',', '.') }}đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <span>Phí vận chuyển</span>
                        <span class="text-success fw-bold">Miễn phí</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4 mt-2">
                        <h5 class="fw-bold">Tổng cộng</h5>
                        <h5 class="fw-bold text-primary cart-total-price">{{ number_format($total, 0, ',', '.') }}đ</h5>
                    </div>
                    <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-lg w-100 rounded-pill py-3 fw-bold">
                        Tiến hành thanh toán
                    </a>
                </div>
            </div>
        @else
            <div class="col-12 text-center py-5">
                <i class="bi bi-cart-x display-1 text-muted"></i>
                <h4 class="mt-4 fw-bold">Giỏ hàng đang trống</h4>
                <p class="text-muted mb-4">Hãy chọn những mẫu thời trang ưng ý nhất cho mình nhé!</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg rounded-pill px-5">
                    Mua sắm ngay
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handle quantity update
            document.querySelectorAll('.btn-update').forEach(button => {
                button.addEventListener('click', function () {
                    const row = this.closest('tr');
                    const productId = row.getAttribute('data-id');
                    const input = row.querySelector('.quantity-input');
                    let quantity = parseInt(input.value);
                    const action = this.getAttribute('data-action');

                    if (action === 'increase') {
                        quantity++;
                    } else if (action === 'decrease' && quantity > 1) {
                        quantity--;
                    }

                    updateCart(productId, quantity, input, row);
                });
            });

            // Handle quantity direct input
            document.querySelectorAll('.quantity-input').forEach(input => {
                input.addEventListener('change', function () {
                    const row = this.closest('tr');
                    const productId = row.getAttribute('data-id');
                    let quantity = parseInt(this.value);

                    if (isNaN(quantity) || quantity < 1) {
                        quantity = 1;
                        this.value = 1;
                    }

                    updateCart(productId, quantity, this, row);
                });
            });

            // Handle item removal
            document.querySelectorAll('.btn-remove').forEach(button => {
                button.addEventListener('click', function () {
                    if (!confirm('Xóa sản phẩm này khỏi giỏ hàng?')) return;

                    const row = this.closest('tr');
                    const productId = row.getAttribute('data-id');

                    fetch('{{ route("cart.remove") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ id: productId })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                row.remove();
                                updateTotals(data.cart_total);

                                if (data.cart_count === 0) {
                                    location.reload();
                                }
                            }
                        });
                });
            });

            function updateCart(id, quantity, input, row) {
                fetch('{{ route("cart.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ id: id, quantity: quantity })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            input.value = quantity;
                            row.querySelector('.item-total-price').textContent = data.item_total;
                            updateTotals(data.cart_total);
                        }
                    });
            }

            function updateTotals(total) {
                document.querySelectorAll('.cart-total-price').forEach(el => {
                    el.textContent = total;
                });
            }
        });
    </script>
@endpush
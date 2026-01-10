@extends('layouts.app')

@section('title', 'Thanh toán - LaraFashion')

@section('content')
<div class="container py-5">
    <div class="row g-5">
        <div class="col-lg-7">
            <h4 class="fw-bold mb-4">Thông tin giao hàng</h4>
            
            @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-medium">Họ và tên</label>
                        <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name', auth()->user()?->name) }}" placeholder="Nhập họ tên người nhận">
                        @error('customer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">Email</label>
                        <input type="email" name="customer_email" class="form-control @error('customer_email') is-invalid @enderror" value="{{ old('customer_email', auth()->user()?->email) }}" placeholder="email@example.com">
                        @error('customer_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">Số điện thoại</label>
                        <input type="text" name="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror" value="{{ old('customer_phone', auth()->user()?->phone_number) }}" placeholder="09xxxxxxxx">
                        @error('customer_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-medium">Địa chỉ giao hàng</label>
                        <textarea name="shipping_address" class="form-control @error('shipping_address') is-invalid @enderror" rows="3" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố">{{ old('shipping_address') }}</textarea>
                        @error('shipping_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <h4 class="fw-bold mt-5 mb-4">Phương thức thanh toán</h4>
                <div class="d-grid gap-3">
                    <div class="form-check card border shadow-sm p-3">
                        <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="cod" value="cod" checked>
                        <label class="form-check-label d-flex align-items-center" for="cod">
                            <i class="bi bi-cash-stack fs-4 text-primary me-3"></i>
                            <div>
                                <span class="fw-bold d-block">Thanh toán khi nhận hàng (COD)</span>
                                <small class="text-muted">Thanh toán bằng tiền mặt khi giao hàng tận nơi</small>
                            </div>
                        </label>
                    </div>
                    <div class="form-check card border shadow-sm p-3">
                        <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="bank" value="bank_transfer">
                        <label class="form-check-label d-flex align-items-center" for="bank">
                            <i class="bi bi-bank fs-4 text-primary me-3"></i>
                            <div>
                                <span class="fw-bold d-block">Chuyển khoản ngân hàng</span>
                                <small class="text-muted">Thông tin tài khoản sẽ hiển thị sau khi đặt hàng</small>
                            </div>
                        </label>
                    </div>
                </div>

                <hr class="my-5">

                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill py-3 fw-bold">
                    Xác nhận đặt hàng
                </button>
            </form>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 100px;">
                <h5 class="fw-bold mb-4">Đơn hàng của bạn</h5>
                <div class="cart-items mb-4">
                    @foreach($cart as $item)
                        <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                            <div class="position-relative">
                                <img src="{{ $item['thumbnail_url'] ?: 'https://placehold.co/60x60?text=No+Image' }}" 
                                     class="rounded" style="width: 64px; height: 64px; object-fit: cover;">
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary">
                                    {{ $item['quantity'] }}
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-bold small text-truncate" style="max-width: 200px;">{{ $item['name'] }}</h6>
                                <p class="text-muted small mb-0">{{ number_format($item['price'], 0, ',', '.') }}đ</p>
                            </div>
                            <div class="text-end">
                                <span class="fw-medium small">{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}đ</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Tạm tính</span>
                    <span class="fw-bold">{{ number_format($total, 0, ',', '.') }}đ</span>
                </div>
                <div class="d-flex justify-content-between mb-4">
                    <span class="text-muted">Giao hàng</span>
                    <span class="text-success fw-bold">Miễn phí</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between my-3">
                    <h5 class="fw-bold">Tổng cộng</h5>
                    <h4 class="fw-bold text-primary">{{ number_format($total, 0, ',', '.') }}đ</h4>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

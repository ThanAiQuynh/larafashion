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
                <input type="hidden" name="voucher_code" id="applied-voucher-code">

                @auth
                    @if($addresses->count() > 0)
                        <div class="mb-4">
                            <label class="form-label fw-medium">Chọn địa chỉ đã lưu</label>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                @foreach($addresses as $address)
                                    <div class="form-check">
                                        <input type="radio" class="btn-check" name="saved_address" 
                                               id="address_{{ $address->id }}" value="{{ $address->id }}"
                                               {{ $address->is_default ? 'checked' : '' }}
                                               data-name="{{ $address->recipient_name }}"
                                               data-phone="{{ $address->recipient_phone }}"
                                               data-address="{{ $address->full_address }}">
                                        <label class="btn btn-outline-primary btn-sm" for="address_{{ $address->id }}">
                                            @if($address->is_default)
                                                <i class="bi bi-star-fill me-1"></i>
                                            @endif
                                            {{ $address->recipient_name }}
                                        </label>
                                    </div>
                                @endforeach
                                <div class="form-check">
                                    <input type="radio" class="btn-check" name="saved_address" id="address_new" value="new">
                                    <label class="btn btn-outline-secondary btn-sm" for="address_new">
                                        <i class="bi bi-plus-lg me-1"></i>Nhập địa chỉ mới
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endif
                @endauth

                <div class="row g-3" id="address-fields">
                    <div class="col-12">
                        <label class="form-label fw-medium">Họ và tên</label>
                        <input type="text" name="customer_name" id="customer_name" class="form-control @error('customer_name') is-invalid @enderror" 
                               value="{{ old('customer_name', $defaultAddress?->recipient_name ?? auth()->user()?->name) }}" placeholder="Nhập họ tên người nhận">
                        @error('customer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">Email</label>
                        <input type="email" name="customer_email" class="form-control @error('customer_email') is-invalid @enderror" value="{{ old('customer_email', auth()->user()?->email) }}" placeholder="email@example.com">
                        @error('customer_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">Số điện thoại</label>
                        <input type="text" name="customer_phone" id="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror" 
                               value="{{ old('customer_phone', $defaultAddress?->recipient_phone ?? auth()->user()?->phone_number) }}" placeholder="09xxxxxxxx">
                        @error('customer_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-medium">Địa chỉ giao hàng</label>
                        <textarea name="shipping_address" id="shipping_address" class="form-control @error('shipping_address') is-invalid @enderror" rows="3" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố">{{ old('shipping_address', $defaultAddress?->full_address) }}</textarea>
                        @error('shipping_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <h4 class="fw-bold mt-5 mb-4">Phương thức thanh toán</h4>
                <div class="d-grid gap-3">
                    <div class="form-check card border shadow-sm p-3">
                        <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="cod" value="cod" checked>
                        <label class="form-check-label d-flex align-items-center" for="cod">
                            <i class="bi bi-cash-stack fs-4 text-success me-3"></i>
                            <div>
                                <span class="fw-bold d-block">Thanh toán khi nhận hàng (COD)</span>
                                <small class="text-muted">Thanh toán bằng tiền mặt khi giao hàng tận nơi</small>
                            </div>
                        </label>
                    </div>
                    <div class="form-check card border shadow-sm p-3">
                        <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="vnpay" value="vnpay">
                        <label class="form-check-label d-flex align-items-center" for="vnpay">
                            <img src="https://vnpay.vn/s1/statics.vnpay.vn/2023/6/0oxhzjmxbksr1686814746087.png" 
                                 alt="VNPay" style="height: 32px;" class="me-3">
                            <div>
                                <span class="fw-bold d-block">Thanh toán qua VNPay</span>
                                <small class="text-muted">ATM, Visa/Master, Ví điện tử, QR Code</small>
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

                <div class="mb-4">
                    <label class="form-label fw-medium">Mã giảm giá</label>
                    <div class="input-group">
                        <input type="text" id="voucher-input" class="form-control" placeholder="Nhập mã voucher (SALE10, ...)">
                        <button class="btn btn-outline-primary" type="button" id="apply-voucher-btn">Áp dụng</button>
                    </div>
                    <div id="voucher-message" class="small mt-1"></div>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Tạm tính</span>
                    <span class="fw-bold" id="subtotal">{{ number_format($total, 0, ',', '.') }}đ</span>
                </div>
                <div id="discount-row" class="d-flex justify-content-between mb-2 d-none">
                    <span class="text-muted">Giảm giá (<span id="discount-name"></span>)</span>
                    <span class="text-danger fw-bold" id="discount-amount">-0đ</span>
                </div>
                <div class="d-flex justify-content-between mb-4">
                    <span class="text-muted">Giao hàng</span>
                    <span class="text-success fw-bold">Miễn phí</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between my-3">
                    <h5 class="fw-bold">Tổng cộng</h5>
                    <h4 class="fw-bold text-primary" id="final-total">{{ number_format($total, 0, ',', '.') }}đ</h4>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addressRadios = document.querySelectorAll('input[name="saved_address"]');
    const nameInput = document.getElementById('customer_name');
    const phoneInput = document.getElementById('customer_phone');
    const addressInput = document.getElementById('shipping_address');
    
    addressRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'new') {
                nameInput.value = '';
                phoneInput.value = '';
                addressInput.value = '';
                nameInput.focus();
            } else {
                nameInput.value = this.dataset.name || '';
                phoneInput.value = this.dataset.phone || '';
                addressInput.value = this.dataset.address || '';
            }
        });
    });

    // Voucher Handling
    const voucherInput = document.getElementById('voucher-input');
    const applyBtn = document.getElementById('apply-voucher-btn');
    const messageDiv = document.getElementById('voucher-message');
    const appliedVoucherHidden = document.getElementById('applied-voucher-code');
    const discountRow = document.getElementById('discount-row');
    const discountName = document.getElementById('discount-name');
    const discountAmount = document.getElementById('discount-amount');
    const finalTotal = document.getElementById('final-total');
    
    // Original total for calculation
    const originalTotal = {{ $total }};

    applyBtn.addEventListener('click', function() {
        const code = voucherInput.value.trim();
        if (!code) return;

        applyBtn.disabled = true;
        applyBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

        fetch('{{ route('checkout.check-voucher') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                code: code,
                order_total: originalTotal
            })
        })
        .then(res => res.json())
        .then(data => {
            applyBtn.disabled = false;
            applyBtn.textContent = 'Áp dụng';

            if (data.success) {
                messageDiv.className = 'small mt-1 text-success';
                messageDiv.textContent = data.message;
                
                appliedVoucherHidden.value = data.data.code;
                discountName.textContent = data.data.code;
                discountAmount.textContent = '-' + data.data.discount_display;
                discountRow.classList.remove('d-none');
                finalTotal.textContent = data.data.new_total_display;
                
                voucherInput.classList.add('is-valid');
                voucherInput.classList.remove('is-invalid');
            } else {
                messageDiv.className = 'small mt-1 text-danger';
                messageDiv.textContent = data.message;
                
                appliedVoucherHidden.value = '';
                discountRow.classList.add('d-none');
                finalTotal.textContent = new Intl.NumberFormat('vi-VN').format(originalTotal) + 'đ';
                
                voucherInput.classList.add('is-invalid');
                voucherInput.classList.remove('is-valid');
            }
        })
        .catch(err => {
            applyBtn.disabled = false;
            applyBtn.textContent = 'Áp dụng';
            alert('Có lỗi xảy ra khi kiểm tra voucher.');
        });
    });
});
</script>
@endpush

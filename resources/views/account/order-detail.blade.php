@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng - LaraFashion')

@section('content')
<div class="container py-5">
    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-lg-3">
            @include('account.partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="mb-4">
                <a href="{{ route('account.orders') }}" class="text-decoration-none text-muted">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách đơn hàng
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Đơn hàng #{{ $order->order_code }}</h5>
                    @switch($order->status)
                        @case('pending') <span class="badge bg-warning text-dark fs-6 py-2 px-3">Chờ xử lý</span> @break
                        @case('confirmed') <span class="badge bg-info fs-6 py-2 px-3">Đã xác nhận</span> @break
                        @case('shipping') <span class="badge bg-primary fs-6 py-2 px-3">Đang giao</span> @break
                        @case('completed') <span class="badge bg-success fs-6 py-2 px-3">Hoàn thành</span> @break
                        @case('cancelled') <span class="badge bg-danger fs-6 py-2 px-3">Đã hủy</span> @break
                    @endswitch
                </div>
                <div class="card-body p-4">
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted small text-uppercase fw-bold mb-2">Thông tin nhận hàng</h6>
                            <p class="mb-1"><strong>{{ $order->customer_name }}</strong></p>
                            <p class="mb-1"><i class="bi bi-telephone me-2 text-muted"></i>{{ $order->customer_phone }}</p>
                            <p class="mb-1"><i class="bi bi-envelope me-2 text-muted"></i>{{ $order->customer_email }}</p>
                            <p class="mb-0"><i class="bi bi-geo-alt me-2 text-muted"></i>{{ is_array($order->shipping_address) ? ($order->shipping_address['address'] ?? '') : $order->shipping_address }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted small text-uppercase fw-bold mb-2">Thông tin đơn hàng</h6>
                            <p class="mb-1"><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                            <p class="mb-1"><strong>Phương thức:</strong> {{ $order->payment_method == 'cod' ? 'Thanh toán khi nhận hàng' : 'Chuyển khoản' }}</p>
                            <p class="mb-0">
                                <strong>Thanh toán:</strong>
                                @switch($order->payment_status)
                                    @case('pending') <span class="badge bg-secondary">Chờ thanh toán</span> @break
                                    @case('paid') <span class="badge bg-success">Đã thanh toán</span> @break
                                    @case('failed') <span class="badge bg-danger">Thất bại</span> @break
                                    @case('refunded') <span class="badge bg-warning text-dark">Hoàn tiền</span> @break
                                @endswitch
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">Sản phẩm</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Sản phẩm</th>
                                <th class="text-center">Đơn giá</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end pe-4">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        @if($item->product)
                                            <img src="{{ $item->product->thumbnail_url ?: 'https://placehold.co/60x60?text=No+Image' }}" 
                                                 class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
                                        <span class="fw-medium">{{ $item->product_name }}</span>
                                    </div>
                                </td>
                                <td class="text-center">{{ number_format($item->unit_price, 0, ',', '.') }}đ</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end pe-4 fw-bold">{{ number_format($item->total_price, 0, ',', '.') }}đ</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-top">
                            <tr>
                                <td colspan="3" class="text-end fw-medium">Tạm tính:</td>
                                <td class="text-end pe-4 fw-bold">{{ number_format($order->items->sum('total_price'), 0, ',', '.') }}đ</td>
                            </tr>
                            @if($order->discount_amount > 0)
                            <tr>
                                <td colspan="3" class="text-end fw-medium text-danger">Giảm giá:</td>
                                <td class="text-end pe-4 fw-bold text-danger">-{{ number_format($order->discount_amount, 0, ',', '.') }}đ</td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="3" class="text-end fw-medium">Phí vận chuyển:</td>
                                <td class="text-end pe-4">{{ number_format($order->shipping_fee ?? 0, 0, ',', '.') }}đ</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end fw-bold fs-5">Tổng cộng:</td>
                                <td class="text-end pe-4 fw-bold fs-5 text-primary">{{ number_format($order->total_amount + ($order->shipping_fee ?? 0), 0, ',', '.') }}đ</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng #' . $order->order_code)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.orders.index') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
        </a>
        <h4 class="mb-0 fw-bold mt-2">Đơn hàng #{{ $order->order_code }}</h4>
    </div>
    <div class="d-flex gap-2">
        @switch($order->status)
            @case('pending')
                <span class="badge bg-warning text-dark fs-6 py-2 px-3">Chờ xử lý</span>
                @break
            @case('confirmed')
                <span class="badge bg-info fs-6 py-2 px-3">Đã xác nhận</span>
                @break
            @case('shipping')
                <span class="badge bg-primary fs-6 py-2 px-3">Đang giao</span>
                @break
            @case('completed')
                <span class="badge bg-success fs-6 py-2 px-3">Hoàn thành</span>
                @break
            @case('cancelled')
                <span class="badge bg-danger fs-6 py-2 px-3">Đã hủy</span>
                @break
        @endswitch
    </div>
</div>


<div class="row g-4">
    <!-- Order Details -->
    <div class="col-lg-8">
        <!-- Products -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Sản phẩm đã đặt</h6>
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
                                    <div>
                                        <span class="fw-medium">{{ $item->product_name }}</span>
                                        @if($item->product)
                                            <br><small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                        @endif
                                    </div>
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

        <!-- Customer Info -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Thông tin khách hàng</h6>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="text-muted small text-uppercase fw-bold mb-3">Người nhận</h6>
                        <p class="mb-1"><strong>{{ $order->customer_name }}</strong></p>
                        <p class="mb-1"><i class="bi bi-telephone me-2 text-muted"></i>{{ $order->customer_phone }}</p>
                        <p class="mb-0"><i class="bi bi-envelope me-2 text-muted"></i>{{ $order->customer_email }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted small text-uppercase fw-bold mb-3">Địa chỉ giao hàng</h6>
                        <p class="mb-0">
                            <i class="bi bi-geo-alt me-2 text-muted"></i>
                            {{ is_array($order->shipping_address) ? ($order->shipping_address['address'] ?? json_encode($order->shipping_address)) : $order->shipping_address }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Order Status Update -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Cập nhật trạng thái</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Trạng thái đơn hàng</label>
                        <select name="status" class="form-select">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                            <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                            <option value="shipping" {{ $order->status == 'shipping' ? 'selected' : '' }}>Đang giao</option>
                            <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Cập nhật trạng thái</button>
                </form>
            </div>
        </div>

        <!-- Payment Status Update -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Thanh toán</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="text-muted small">Phương thức:</span>
                    <span class="fw-medium ms-2">
                        {{ $order->payment_method == 'cod' ? 'Thanh toán khi nhận hàng (COD)' : 'Chuyển khoản ngân hàng' }}
                    </span>
                </div>
                <form action="{{ route('admin.orders.update-payment', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Trạng thái thanh toán</label>
                        <select name="payment_status" class="form-select">
                            <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>Chờ thanh toán</option>
                            <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                            <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Thất bại</option>
                            <option value="refunded" {{ $order->payment_status == 'refunded' ? 'selected' : '' }}>Hoàn tiền</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-outline-primary w-100">Cập nhật thanh toán</button>
                </form>
            </div>
        </div>

        <!-- Order Info -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Thông tin đơn hàng</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Ngày đặt hàng:</span>
                    <span class="fw-medium">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Cập nhật lần cuối:</span>
                    <span class="fw-medium">{{ $order->updated_at->format('d/m/Y H:i') }}</span>
                </div>
                @if($order->note)
                <div class="mt-3 pt-3 border-top">
                    <span class="text-muted small d-block mb-1">Ghi chú:</span>
                    <p class="mb-0">{{ $order->note }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

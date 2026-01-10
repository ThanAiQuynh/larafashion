@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Quản lý đơn hàng</h4>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4">
        {{ session('success') }}
    </div>
@endif

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label small text-muted">Tìm kiếm</label>
                <input type="text" name="search" class="form-control" placeholder="Mã đơn, tên, SĐT, email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                    <option value="shipping" {{ request('status') == 'shipping' ? 'selected' : '' }}>Đang giao</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">Thanh toán</label>
                <select name="payment_status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Chờ thanh toán</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                    <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Thất bại</option>
                    <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Hoàn tiền</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i> Lọc
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Thanh toán</th>
                    <th>Ngày đặt</th>
                    <th class="text-end pe-4">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td class="ps-4">
                            <a href="{{ route('admin.orders.show', $order) }}" class="fw-bold text-primary text-decoration-none">
                                {{ $order->order_code }}
                            </a>
                        </td>
                        <td>
                            <div class="fw-medium">{{ $order->customer_name ?: ($order->user?->name ?? 'Khách vãng lai') }}</div>
                            <small class="text-muted">{{ $order->customer_phone }}</small>
                        </td>
                        <td class="fw-bold">{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
                        <td>
                            @switch($order->status)
                                @case('pending')
                                    <span class="badge bg-warning text-dark">Chờ xử lý</span>
                                    @break
                                @case('confirmed')
                                    <span class="badge bg-info">Đã xác nhận</span>
                                    @break
                                @case('shipping')
                                    <span class="badge bg-primary">Đang giao</span>
                                    @break
                                @case('completed')
                                    <span class="badge bg-success">Hoàn thành</span>
                                    @break
                                @case('cancelled')
                                    <span class="badge bg-danger">Đã hủy</span>
                                    @break
                            @endswitch
                        </td>
                        <td>
                            @switch($order->payment_status)
                                @case('pending')
                                    <span class="badge bg-secondary">Chờ thanh toán</span>
                                    @break
                                @case('paid')
                                    <span class="badge bg-success">Đã thanh toán</span>
                                    @break
                                @case('failed')
                                    <span class="badge bg-danger">Thất bại</span>
                                    @break
                                @case('refunded')
                                    <span class="badge bg-warning text-dark">Hoàn tiền</span>
                                    @break
                            @endswitch
                        </td>
                        <td>
                            <small>{{ $order->created_at->format('d/m/Y H:i') }}</small>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Chi tiết
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-inbox display-4 text-muted"></i>
                            <p class="text-muted mt-3">Không có đơn hàng nào.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4 d-flex justify-content-center">
    {{ $orders->links() }}
</div>
@endsection

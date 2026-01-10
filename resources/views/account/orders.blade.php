@extends('layouts.app')

@section('title', 'Đơn hàng của tôi - LaraFashion')

@section('content')
<div class="container py-5">
    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-lg-3">
            @include('account.partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Đơn hàng của tôi</h5>
                </div>
                <div class="card-body p-0">
                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Mã đơn</th>
                                        <th>Ngày đặt</th>
                                        <th>Sản phẩm</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th class="text-end pe-4"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td class="ps-4 fw-medium">{{ $order->order_code }}</td>
                                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $order->items->count() }} sản phẩm</td>
                                            <td class="fw-bold">{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
                                            <td>
                                                @switch($order->status)
                                                    @case('pending') <span class="badge bg-warning text-dark">Chờ xử lý</span> @break
                                                    @case('confirmed') <span class="badge bg-info">Đã xác nhận</span> @break
                                                    @case('shipping') <span class="badge bg-primary">Đang giao</span> @break
                                                    @case('completed') <span class="badge bg-success">Hoàn thành</span> @break
                                                    @case('cancelled') <span class="badge bg-danger">Đã hủy</span> @break
                                                @endswitch
                                            </td>
                                            <td class="text-end pe-4">
                                                <a href="{{ route('account.orders.detail', $order) }}" class="btn btn-sm btn-outline-primary">Chi tiết</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-bag-x display-4 text-muted"></i>
                            <p class="text-muted mt-3">Bạn chưa có đơn hàng nào.</p>
                            <a href="{{ route('products.index') }}" class="btn btn-primary rounded-pill px-4">Mua sắm ngay</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

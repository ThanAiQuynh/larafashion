@extends('layouts.app')

@section('title', 'Đặt hàng thành công - LaraFashion')

@section('content')
<div class="container py-5 text-center">
    <div class="mb-5">
        <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
        <h2 class="fw-bold mt-4">Đặt hàng thành công!</h2>
        <p class="text-muted lead">Cảm ơn bạn đã mua sắm tại LaraFashion. Mã đơn hàng của bạn là <strong>#{{ $order->id }}</strong></p>
    </div>

    <div class="row justify-content-center mb-5 text-start">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-4">Chi tiết đơn hàng</h5>
                
                <div class="mb-4">
                    <h6 class="text-muted small text-uppercase fw-bold mb-3">Người nhận</h6>
                    <p class="mb-1"><strong>{{ $order->customer_name }}</strong></p>
                    <p class="mb-1">{{ $order->customer_phone }}</p>
                    <p class="mb-1">{{ $order->customer_email }}</p>
                    <p class="mb-0">{{ is_array($order->shipping_address) ? ($order->shipping_address['address'] ?? '') : $order->shipping_address }}</p>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted small text-uppercase fw-bold mb-3">Thanh toán</h6>
                    <p class="mb-0">
                        @if($order->payment_method == 'cod')
                            Thanh toán khi nhận hàng (COD)
                        @else
                            Chuyển khoản ngân hàng
                            <div class="p-3 bg-light rounded mt-2 border">
                                <small class="d-block mb-1 text-primary fw-bold">Thông tin tài khoản:</small>
                                <small class="d-block text-muted">Ngân hàng: Vietcombank</small>
                                <small class="d-block text-muted">Số tài khoản: 123456789</small>
                                <small class="d-block text-muted">Chủ tài khoản: LaraFashion Store</small>
                                <small class="d-block text-muted">Nội dung: CK{{ $order->id }}</small>
                            </div>
                        @endif
                    </p>
                </div>

                <table class="table table-borderless table-sm mb-0">
                    <tbody class="small">
                        @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product->name }} x {{ $item->quantity }}</td>
                            <td class="text-end">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2"><hr></td>
                        </tr>
                        <tr>
                            <td class="fw-bold fs-5">Tổng cộng</td>
                            <td class="text-end fw-bold fs-5 text-primary">{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center gap-3">
        <a href="{{ route('home') }}" class="btn btn-primary rounded-pill px-5">Về trang chủ</a>
        <a href="{{ route('products.index') }}" class="btn btn-outline-dark rounded-pill px-4">Tiếp tục mua sắm</a>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Tài khoản - LaraFashion')

@section('content')
<div class="container py-5">
    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px;">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-person-fill text-white fs-1"></i>
                        </div>
                        <h5 class="fw-bold mb-0">{{ Auth::user()->name }}</h5>
                        <small class="text-muted">{{ Auth::user()->email }}</small>
                    </div>
                    <nav class="nav flex-column">
                        <a class="nav-link px-3 py-2 rounded {{ request()->routeIs('account.index') ? 'bg-primary text-white' : 'text-dark' }}" href="{{ route('account.index') }}">
                            <i class="bi bi-grid me-2"></i> Tổng quan
                        </a>
                        <a class="nav-link px-3 py-2 rounded {{ request()->routeIs('account.orders*') ? 'bg-primary text-white' : 'text-dark' }}" href="{{ route('account.orders') }}">
                            <i class="bi bi-bag me-2"></i> Đơn hàng
                        </a>
                        <a class="nav-link px-3 py-2 rounded {{ request()->routeIs('account.profile') ? 'bg-primary text-white' : 'text-dark' }}" href="{{ route('account.profile') }}">
                            <i class="bi bi-person me-2"></i> Thông tin
                        </a>
                        <a class="nav-link px-3 py-2 rounded {{ request()->routeIs('account.password') ? 'bg-primary text-white' : 'text-dark' }}" href="{{ route('account.password') }}">
                            <i class="bi bi-shield-lock me-2"></i> Đổi mật khẩu
                        </a>
                        <hr>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="nav-link px-3 py-2 text-danger w-100 text-start border-0 bg-transparent">
                                <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                            </button>
                        </form>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm mb-4">{{ session('error') }}</div>
            @endif

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Xin chào, {{ Auth::user()->name }}!</h5>
                    <p class="text-muted mb-0">Chào mừng bạn quay lại với LaraFashion. Quản lý tài khoản, theo dõi đơn hàng và cập nhật thông tin cá nhân tại đây.</p>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Đơn hàng gần đây</h6>
                    <a href="{{ route('account.orders') }}" class="text-decoration-none small">Xem tất cả <i class="bi bi-arrow-right"></i></a>
                </div>
                <div class="card-body p-0">
                    @if($recentOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Mã đơn</th>
                                        <th>Ngày đặt</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th class="text-end pe-4"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                        <tr>
                                            <td class="ps-4 fw-medium">{{ $order->order_code }}</td>
                                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
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

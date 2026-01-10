@extends('layouts.admin')

@section('title', 'Chi tiết người dùng')
@section('page-title', 'Chi tiết người dùng')

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <!-- User Profile Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center">
                    <div class="bg-{{ $user->isAdmin() ? 'danger' : 'primary' }} bg-opacity-10 text-{{ $user->isAdmin() ? 'danger' : 'primary' }} rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-{{ $user->isAdmin() ? 'shield-check' : 'person' }} fs-1"></i>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-2">{{ $user->email }}</p>
                    @if($user->isAdmin())
                        <span class="badge bg-danger">Admin</span>
                    @else
                        <span class="badge bg-info">Khách hàng</span>
                    @endif
                    <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">
                        {{ $user->is_active ? 'Active' : 'Locked' }}
                    </span>
                </div>
                <hr class="my-0">
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Số điện thoại</small>
                        <span>{{ $user->phone_number ?: 'Chưa cập nhật' }}</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Ngày tạo</small>
                        <span>{{ $user->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Cập nhật lần cuối</small>
                        <span>{{ $user->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary w-100">
                        <i class="bi bi-pencil me-1"></i> Chỉnh sửa
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Stats -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="text-primary mb-0">{{ $user->orders->count() }}</h3>
                            <small class="text-muted">Đơn hàng</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="text-success mb-0">{{ number_format($user->orders->where('status', 'completed')->sum('total_amount'), 0, ',', '.') }}đ</h3>
                            <small class="text-muted">Tổng chi tiêu</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="text-warning mb-0">{{ $user->reviews->count() }}</h3>
                            <small class="text-muted">Đánh giá</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-receipt me-2"></i>Đơn hàng gần đây</h6>
                </div>
                <div class="card-body p-0">
                    @if($user->orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Ngày đặt</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->orders as $order)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_code }}</a>
                                            </td>
                                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                            <td>{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
                                            <td>
                                                @php
                                                    $statusConfig = [
                                                        'pending' => ['bg' => 'warning', 'text' => 'Chờ xác nhận'],
                                                        'confirmed' => ['bg' => 'info', 'text' => 'Đã xác nhận'],
                                                        'shipping' => ['bg' => 'primary', 'text' => 'Đang giao'],
                                                        'completed' => ['bg' => 'success', 'text' => 'Hoàn thành'],
                                                        'cancelled' => ['bg' => 'danger', 'text' => 'Đã hủy'],
                                                    ];
                                                    $config = $statusConfig[$order->status] ?? ['bg' => 'secondary', 'text' => $order->status];
                                                @endphp
                                                <span class="badge bg-{{ $config['bg'] }}">{{ $config['text'] }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox display-6"></i>
                            <p class="mt-2">Chưa có đơn hàng nào</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Reviews -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-star me-2"></i>Đánh giá gần đây</h6>
                </div>
                <div class="card-body p-0">
                    @if($user->reviews->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($user->reviews as $review)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-medium">{{ $review->product->name ?? 'Sản phẩm đã xóa' }}</div>
                                            <div class="text-warning small">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                                @endfor
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ $review->created_at->format('d/m/Y') }}</small>
                                    </div>
                                    @if($review->comment)
                                        <p class="text-muted small mb-0 mt-1">{{ Str::limit($review->comment, 100) }}</p>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-chat-square display-6"></i>
                            <p class="mt-2">Chưa có đánh giá nào</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
    </div>
@endsection

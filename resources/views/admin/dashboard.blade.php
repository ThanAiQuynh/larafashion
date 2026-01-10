@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Tổng quan')

@section('content')
    <!-- Stats Cards Row 1 -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <div class="value">{{ number_format($stats['products']) }}</div>
                    <div class="label">Sản phẩm</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-receipt"></i>
                </div>
                <div>
                    <div class="value">{{ number_format($stats['orders']) }}</div>
                    <div class="label">Đơn hàng</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <div class="value">{{ number_format($stats['customers']) }}</div>
                    <div class="label">Khách hàng</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <div class="value">{{ number_format($stats['pending_orders']) }}</div>
                    <div class="label">Đơn chờ xử lý</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="icon bg-success bg-opacity-10 text-success me-3" style="width: 60px; height: 60px; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div>
                        <div class="text-muted mb-1">Tổng doanh thu</div>
                        <div class="fs-4 fw-bold text-success">{{ number_format($stats['revenue'], 0, ',', '.') }}đ</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="icon bg-primary bg-opacity-10 text-primary me-3" style="width: 60px; height: 60px; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                        <i class="bi bi-calendar-day"></i>
                    </div>
                    <div>
                        <div class="text-muted mb-1">Doanh thu hôm nay</div>
                        <div class="fs-4 fw-bold text-primary">{{ number_format($stats['revenue_today'], 0, ',', '.') }}đ</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="icon bg-info bg-opacity-10 text-info me-3" style="width: 60px; height: 60px; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                        <i class="bi bi-cart-check"></i>
                    </div>
                    <div>
                        <div class="text-muted mb-1">Đơn hàng hôm nay</div>
                        <div class="fs-4 fw-bold">{{ $stats['orders_today'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Revenue Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Doanh thu 6 tháng gần nhất</h6>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <!-- Order Status Chart -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Trạng thái đơn hàng</h6>
                </div>
                <div class="card-body">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Top Products -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-trophy me-2"></i>Top sản phẩm bán chạy</h6>
                </div>
                <div class="card-body p-0">
                    @if($topProducts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Sản phẩm</th>
                                        <th class="text-end">Đã bán</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topProducts as $index => $product)
                                        <tr>
                                            <td><span class="badge bg-{{ $index < 3 ? 'warning' : 'secondary' }}">{{ $index + 1 }}</span></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $product->thumbnail_url ?: 'https://placehold.co/40x40' }}" 
                                                         class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                    <span class="text-truncate" style="max-width: 200px;">{{ $product->name }}</span>
                                                </div>
                                            </td>
                                            <td class="text-end fw-bold">{{ number_format($product->total_sold) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox display-6"></i>
                            <p class="mt-2">Chưa có dữ liệu</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Low Stock Products -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-exclamation-triangle text-warning me-2"></i>Sản phẩm sắp hết hàng</h6>
                </div>
                <div class="card-body p-0">
                    @if($lowStockProducts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th class="text-end">Còn lại</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStockProducts as $product)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $product->thumbnail_url ?: 'https://placehold.co/40x40' }}" 
                                                         class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                    <span class="text-truncate" style="max-width: 200px;">{{ $product->name }}</span>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <span class="badge bg-{{ $product->stock_quantity < 5 ? 'danger' : 'warning' }}">
                                                    {{ $product->stock_quantity }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-check-circle text-success display-6"></i>
                            <p class="mt-2">Tất cả SP đều đủ hàng</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Orders -->
        <div class="col-12">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Đơn hàng gần đây</h6>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                </div>
                <div class="card-body p-0">
                    @if($recentOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Khách hàng</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.orders.show', $order) }}" class="fw-medium">
                                                    {{ $order->order_code }}
                                                </a>
                                            </td>
                                            <td>{{ $order->user?->name ?? $order->customer_name ?? 'Khách' }}</td>
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
                        <div class="text-center py-4">
                            <i class="bi bi-inbox display-6 text-muted"></i>
                            <p class="mt-2 text-muted">Chưa có đơn hàng</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueData = @json($revenueByMonth);
    if (revenueData.length > 0) {
        new Chart(document.getElementById('revenueChart'), {
            type: 'bar',
            data: {
                labels: revenueData.map(item => item.label),
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: revenueData.map(item => item.value),
                    backgroundColor: 'rgba(79, 70, 229, 0.8)',
                    borderColor: 'rgb(79, 70, 229)',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return new Intl.NumberFormat('vi-VN').format(context.raw) + 'đ';
                            }
                        }
                    }
                }
            }
        });
    }

    // Order Status Chart
    const ordersByStatus = @json($ordersByStatus);
    const statusLabels = {
        'pending': 'Chờ xác nhận',
        'confirmed': 'Đã xác nhận',
        'shipping': 'Đang giao',
        'completed': 'Hoàn thành',
        'cancelled': 'Đã hủy'
    };
    const statusColors = {
        'pending': '#f59e0b',
        'confirmed': '#0ea5e9',
        'shipping': '#6366f1',
        'completed': '#10b981',
        'cancelled': '#ef4444'
    };
    
    new Chart(document.getElementById('orderStatusChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(ordersByStatus).map(key => statusLabels[key] || key),
            datasets: [{
                data: Object.values(ordersByStatus),
                backgroundColor: Object.keys(ordersByStatus).map(key => statusColors[key] || '#64748b'),
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
});
</script>
@endpush

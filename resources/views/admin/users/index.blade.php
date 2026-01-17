@extends('layouts.admin')

@section('title', 'Quản lý Người dùng')
@section('page-title', 'Quản lý Người dùng')

@section('content')
    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                    <div>
                        <h4 class="mb-0">{{ number_format($stats['total']) }}</h4>
                        <small class="text-muted">Tổng người dùng</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-danger bg-opacity-10 text-danger p-3 rounded">
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>
                    <div>
                        <h4 class="mb-0">{{ number_format($stats['admins']) }}</h4>
                        <small class="text-muted">Admin</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded">
                        <i class="bi bi-person fs-4"></i>
                    </div>
                    <div>
                        <h4 class="mb-0">{{ number_format($stats['customers']) }}</h4>
                        <small class="text-muted">Khách hàng</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded">
                        <i class="bi bi-check-circle fs-4"></i>
                    </div>
                    <div>
                        <h4 class="mb-0">{{ number_format($stats['active']) }}</h4>
                        <small class="text-muted">Đang hoạt động</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Actions -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small">Tìm kiếm</label>
                    <input type="text" name="search" class="form-control" placeholder="Tên, email, SĐT..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Vai trò</label>
                    <select name="role" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Khách hàng</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Đã khóa</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i> Lọc
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
                </div>
                <div class="col-md-2 text-end">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-lg me-1"></i> Thêm mới
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Người dùng</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Vai trò</th>
                            <th>Đơn hàng</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th width="120">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-{{ $user->isAdmin() ? 'danger' : 'primary' }} bg-opacity-10 text-{{ $user->isAdmin() ? 'danger' : 'primary' }} rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="bi bi-{{ $user->isAdmin() ? 'shield-check' : 'person' }}"></i>
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $user->name }}</div>
                                            @if($user->id === auth()->id())
                                                <small class="text-muted">(Bạn)</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone_number ?: '-' }}</td>
                                <td>
                                    @if($user->isAdmin())
                                        <span class="badge bg-danger">Admin</span>
                                    @else
                                        <span class="badge bg-info">Khách hàng</span>
                                    @endif
                                </td>
                                <td>{{ $user->orders_count }}</td>
                                <td>
                                    <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-success' : 'btn-secondary' }}"
                                                {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                            {{ $user->is_active ? 'Active' : 'Locked' }}
                                        </button>
                                    </form>
                                </td>
                                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-info" title="Xem">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Sửa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($user->id !== auth()->id() && $user->orders_count === 0)
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa"
                                                onclick="return confirm('Xóa người dùng này?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-people display-4"></i>
                                    <p class="mt-2">Không tìm thấy người dùng nào</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $users->links() }}
    </div>

    <style>
        /* Hide pagination summary text */
        nav div.d-none.flex-sm-fill.d-sm-flex > div:first-child {
            display: none !important;
        }
        
        nav .justify-content-sm-between {
            justify-content: center !important;
        }
    </style>
@endsection

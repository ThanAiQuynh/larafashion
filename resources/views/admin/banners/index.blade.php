@extends('layouts.admin')

@section('title', 'Quản lý Banner')
@section('page-title', 'Quản lý Banner')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-muted mb-0">Quản lý banner hiển thị trên trang chủ</p>
        </div>
        <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Thêm banner
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="80">Vị trí</th>
                            <th width="150">Ảnh</th>
                            <th>Tiêu đề</th>
                            <th>Link</th>
                            <th width="100">Trạng thái</th>
                            <th width="150">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($banners as $banner)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $banner->position }}</span></td>
                                <td>
                                    <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" 
                                         class="rounded" style="height: 60px; width: 100px; object-fit: cover;">
                                </td>
                                <td class="fw-medium">{{ $banner->title }}</td>
                                <td>
                                    @if($banner->link_url)
                                        <a href="{{ $banner->link_url }}" target="_blank" class="text-truncate d-block" style="max-width: 200px;">
                                            {{ $banner->link_url }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.banners.toggle-status', $banner) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $banner->is_active ? 'btn-success' : 'btn-secondary' }}">
                                            {{ $banner->is_active ? 'Hiện' : 'Ẩn' }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Xóa banner này?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-image display-4"></i>
                                    <p class="mt-2">Chưa có banner nào</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $banners->links() }}
    </div>
@endsection

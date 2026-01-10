@extends('layouts.admin')

@section('title', 'Quản lý danh mục')
@section('page-title', 'Danh mục sản phẩm')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Danh sách danh mục</h5>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">Thêm danh mục</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Tên danh mục</th>
                        <th>Slug</th>
                        <th>Danh mục cha</th>
                        <th>Số sản phẩm</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td class="ps-4">
                            @if($category->parent_id)
                                <span class="text-muted ms-3">|-- {{ $category->name }}</span>
                            @else
                                <strong>{{ $category->name }}</strong>
                            @endif
                        </td>
                        <td><code>{{ $category->slug }}</code></td>
                        <td>{{ $category->parent ? $category->parent->name : '-' }}</td>
                        <td>{{ $category->products_count }}</td>
                        <td>
                            @if($category->is_active)
                                <span class="badge bg-success">Hoạt động</span>
                            @else
                                <span class="badge bg-secondary">Vô hiệu</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary me-1">Sửa</a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">Chưa có danh mục nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

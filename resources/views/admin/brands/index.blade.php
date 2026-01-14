@extends('layouts.admin')

@section('title', 'Quản lý thương hiệu')
@section('page-title', 'Thương hiệu sản phẩm')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Danh sách thương hiệu</h5>
            <a href="{{ route('admin.brands.create') }}" class="btn btn-primary btn-sm">Thêm thương hiệu</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Logo</th>
                            <th>Tên thương hiệu</th>
                            <th>Slug</th>
                            <th>Số sản phẩm</th>
                            <th class="text-end pe-4">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($brands as $brand)
                            <tr>
                                <td class="ps-4">
                                    @if($brand->logo_url)
                                        <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" class="rounded"
                                            style="width: 40px; height: 40px; object-fit: contain;">
                                    @else
                                        <span class="badge bg-light text-muted">N/A</span>
                                    @endif
                                </td>
                                <td><strong>{{ $brand->name }}</strong></td>
                                <td><code>{{ $brand->slug }}</code></td>
                                <td>{{ $brand->products_count }}</td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.brands.edit', $brand) }}"
                                        class="btn btn-sm btn-outline-primary me-1" title="Sửa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa"
                                            onclick="return confirm('Bạn có chắc muốn xóa?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">Chưa có thương hiệu nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
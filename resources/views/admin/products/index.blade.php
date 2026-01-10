@extends('layouts.admin')

@section('title', 'Quản lý sản phẩm')
@section('page-title', 'Quản lý sản phẩm')

@section('content')
    <!-- Actions Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Thêm sản phẩm
        </a>
        
        <span class="text-muted">Tổng: {{ $products->total() }} sản phẩm</span>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.products.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Tìm theo tên, SKU..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="category" class="form-select">
                        <option value="">Tất cả danh mục</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @foreach($category->children as $child)
                                <option value="{{ $child->id }}" {{ request('category') == $child->id ? 'selected' : '' }}>
                                    &nbsp;&nbsp;-- {{ $child->name }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="brand" class="form-select">
                        <option value="">Tất cả thương hiệu</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang bán</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Ngừng bán</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search me-1"></i> Lọc
                    </button>
                    @if(request()->hasAny(['search', 'category', 'brand', 'status']))
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg"></i> Xóa lọc
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card">
        <div class="card-body p-0">
            @if($products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 80px;">Ảnh</th>
                                <th>Sản phẩm</th>
                                <th>Danh mục</th>
                                <th>Giá</th>
                                <th class="text-center">Kho</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center" style="width: 150px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td>
                                        <img src="{{ $product->thumbnail_url ?: 'https://placehold.co/60x60/e2e8f0/64748b?text=No' }}" 
                                             alt="{{ $product->name }}" 
                                             class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ $product->name }}</div>
                                        <small class="text-muted">SKU: {{ $product->sku }}</small>
                                        @if($product->is_featured)
                                            <span class="badge bg-warning text-dark ms-1">Nổi bật</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $product->category?->name ?? '-' }}</div>
                                        <small class="text-muted">{{ $product->brand?->name }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-medium text-primary">{{ number_format($product->price, 0, ',', '.') }}đ</div>
                                        @if($product->original_price)
                                            <small class="text-muted text-decoration-line-through">
                                                {{ number_format($product->original_price, 0, ',', '.') }}đ
                                            </small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($product->stock_quantity > 10)
                                            <span class="text-success">{{ $product->stock_quantity }}</span>
                                        @elseif($product->stock_quantity > 0)
                                            <span class="text-warning">{{ $product->stock_quantity }}</span>
                                        @else
                                            <span class="text-danger">Hết hàng</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.products.toggle-status', $product) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $product->is_active ? 'btn-success' : 'btn-secondary' }}">
                                                {{ $product->is_active ? 'Đang bán' : 'Ngừng bán' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.products.edit', $product) }}" 
                                               class="btn btn-outline-primary" title="Sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.products.destroy', $product) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Xóa">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="card-footer">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-box-seam display-4 text-muted"></i>
                    <p class="mt-3 text-muted">Chưa có sản phẩm nào</p>
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Thêm sản phẩm đầu tiên
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection

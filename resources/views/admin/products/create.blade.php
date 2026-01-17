@extends('layouts.admin')

@section('title', 'Thêm sản phẩm')
@section('page-title', 'Thêm sản phẩm mới')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="card mb-4">
                    <div class="card-header">Thông tin sản phẩm</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required
                                       oninvalid="this.setCustomValidity('Vui lòng nhập tên sản phẩm.')"
                                       oninput="this.setCustomValidity('')">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="sku" class="form-label">Mã SKU <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                       id="sku" name="sku" value="{{ old('sku') }}" required
                                       oninvalid="this.setCustomValidity('Vui lòng nhập mã SKU.')"
                                       oninput="this.setCustomValidity('')">
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="category_id" class="form-label">Danh mục</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">-- Chọn danh mục --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                        @foreach($category->children as $child)
                                            <option value="{{ $child->id }}" {{ old('category_id') == $child->id ? 'selected' : '' }}>
                                                &nbsp;&nbsp;-- {{ $child->name }}
                                            </option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="brand_id" class="form-label">Thương hiệu</label>
                                <select class="form-select" id="brand_id" name="brand_id">
                                    <option value="">-- Chọn thương hiệu --</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label">Mô tả</label>
                                <textarea class="form-control" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header">Giá & Kho hàng</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="price" class="form-label">Giá bán <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                           id="price" name="price" value="{{ old('price') }}" min="0" required
                                           oninvalid="this.setCustomValidity('Vui lòng nhập giá bán.')"
                                           oninput="this.setCustomValidity('')">
                                    <span class="input-group-text">đ</span>
                                </div>
                                @error('price')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="original_price" class="form-label">Giá gốc</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" 
                                           id="original_price" name="original_price" value="{{ old('original_price') }}" min="0">
                                    <span class="input-group-text">đ</span>
                                </div>
                                <small class="text-muted">Để trống nếu không giảm giá</small>
                            </div>
                            <div class="col-md-4">
                                <label for="stock_quantity" class="form-label">Số lượng kho</label>
                                <input type="number" class="form-control" 
                                       id="stock_quantity" name="stock_quantity" value="0" readonly>
                                <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Số lượng được quản lý qua Phiếu nhập hàng</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header">Hình ảnh</div>
                    <div class="card-body">
                        <label for="thumbnail_file" class="form-label">Tải ảnh lên</label>
                        <div class="d-flex gap-3 align-items-start">
                            <div class="flex-grow-1">
                                <input type="file" class="form-control @error('thumbnail_file') is-invalid @enderror" 
                                       id="thumbnail_file" name="thumbnail_file" accept="image/*" onchange="previewImage(this)">
                                @error('thumbnail_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Định dạng: JPG, PNG, GIF, WebP. Tối đa 2MB</small>
                            </div>
                            <div id="image-preview" class="border rounded bg-light d-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px; min-width: 80px; overflow: hidden;">
                                <img id="preview-img" src="" class="img-fluid" style="max-width: 100%; max-height: 100%; display: none;">
                                <i class="bi bi-image text-muted" id="preview-placeholder"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header">Cài đặt</div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Đang bán</label>
                        </div>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_featured" value="0">
                            <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1"
                                   {{ old('is_featured') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">Sản phẩm nổi bật</label>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Biến thể sản phẩm (Size & Màu sắc)</span>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addVariantRow()">
                            <i class="bi bi-plus-lg me-1"></i>Thêm biến thể
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info small mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            Thêm các biến thể nếu sản phẩm có nhiều size hoặc màu sắc. Mỗi biến thể có thể có số lượng kho riêng và điều chỉnh giá.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle" id="variants-table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 100px;">Size</th>
                                        <th style="width: 120px;">Màu sắc</th>
                                        <th style="width: 80px;">Mã màu</th>
                                        <th style="width: 100px;">Số lượng</th>
                                        <th style="width: 120px;">Điều chỉnh giá</th>
                                        <th style="width: 120px;">SKU riêng</th>
                                        <th style="width: 60px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="variants-body">
                                    <!-- Variant rows will be added here -->
                                </tbody>
                            </table>
                        </div>
                        <p class="text-muted small mb-0">
                            <strong>Gợi ý Size:</strong> S, M, L, XL, XXL<br>
                            <strong>Điều chỉnh giá:</strong> Nhập số âm để giảm giá, số dương để tăng giá so với giá gốc.
                        </p>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Thêm sản phẩm
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    const previewImg = document.getElementById('preview-img');
    const placeholder = document.getElementById('preview-placeholder');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewImg.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        previewImg.style.display = 'none';
        if (placeholder) placeholder.style.display = 'block';
    }
}

// Variant management
let variantIndex = 0;

function addVariantRow(data = {}) {
    const tbody = document.getElementById('variants-body');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <select class="form-select form-select-sm" name="variants[${variantIndex}][size]">
                <option value="">-- Chọn --</option>
                <option value="S" ${data.size === 'S' ? 'selected' : ''}>S</option>
                <option value="M" ${data.size === 'M' ? 'selected' : ''}>M</option>
                <option value="L" ${data.size === 'L' ? 'selected' : ''}>L</option>
                <option value="XL" ${data.size === 'XL' ? 'selected' : ''}>XL</option>
                <option value="XXL" ${data.size === 'XXL' ? 'selected' : ''}>XXL</option>
            </select>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="variants[${variantIndex}][color]" 
                   placeholder="VD: Đen, Trắng" value="${data.color || ''}">
        </td>
        <td>
            <input type="color" class="form-control form-control-sm form-control-color" 
                   name="variants[${variantIndex}][color_code]" value="${data.color_code || '#000000'}" 
                   style="width: 50px; padding: 2px;">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm" name="variants[${variantIndex}][stock_quantity]" 
                   value="0" readonly>
        </td>
        <td>
            <div class="input-group input-group-sm">
                <input type="number" class="form-control form-control-sm" name="variants[${variantIndex}][price_adjustment]" 
                       value="${data.price_adjustment || 0}">
                <span class="input-group-text">đ</span>
            </div>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="variants[${variantIndex}][sku]" 
                   placeholder="Tự động" value="${data.sku || ''}">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeVariantRow(this)">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    variantIndex++;
}

function removeVariantRow(btn) {
    btn.closest('tr').remove();
}
</script>
@endpush


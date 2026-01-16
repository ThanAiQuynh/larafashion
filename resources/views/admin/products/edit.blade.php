@extends('layouts.admin')

@section('title', 'Sửa sản phẩm')
@section('page-title', 'Sửa sản phẩm')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="card mb-4">
                    <div class="card-header">Thông tin sản phẩm</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="sku" class="form-label">Mã SKU <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                       id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required>
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="category_id" class="form-label">Danh mục</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">-- Chọn danh mục --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                        @foreach($category->children as $child)
                                            <option value="{{ $child->id }}" {{ old('category_id', $product->category_id) == $child->id ? 'selected' : '' }}>
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
                                        <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label">Mô tả</label>
                                <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $product->description) }}</textarea>
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
                                           id="price" name="price" value="{{ old('price', $product->price) }}" min="0" required>
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
                                           id="original_price" name="original_price" value="{{ old('original_price', $product->original_price) }}" min="0">
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="stock_quantity" class="form-label">Số lượng kho <span class="text-danger">*</span></label>
                                @if($product->hasVariants())
                                    <input type="number" class="form-control bg-light" 
                                           value="{{ $product->getTotalStock() }}" readonly disabled>
                                    <input type="hidden" name="stock_quantity" value="{{ $product->stock_quantity }}">
                                    <small class="text-info">
                                        <i class="bi bi-info-circle"></i> 
                                        Tổng kho = {{ $product->getTotalStock() }} (từ {{ $product->variants->count() }} biến thể)
                                    </small>
                                @else
                                    <input type="number" class="form-control" 
                                           id="stock_quantity" name="stock_quantity" value="{{ $product->stock_quantity }}" readonly>
                                    <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Số lượng được quản lý qua Phiếu nhập hàng</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header">Hình ảnh</div>
                    <div class="card-body">
                        @if($product->thumbnail_url)
                            <div class="mb-3">
                                <label class="form-label small text-muted">Ảnh hiện tại:</label>
                                <div><img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="rounded" style="max-height: 150px;"></div>
                            </div>
                        @endif
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="thumbnail_file" class="form-label">Tải ảnh mới</label>
                                <input type="file" class="form-control @error('thumbnail_file') is-invalid @enderror" 
                                       id="thumbnail_file" name="thumbnail_file" accept="image/*" onchange="previewImage(this)">
                                @error('thumbnail_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Định dạng: JPG, PNG, GIF, WebP. Tối đa 2MB</small>
                            </div>
                            <div class="col-md-6">
                                <label for="thumbnail_url" class="form-label">Hoặc nhập URL ảnh</label>
                                <input type="url" class="form-control @error('thumbnail_url') is-invalid @enderror" 
                                       id="thumbnail_url" name="thumbnail_url" value="{{ old('thumbnail_url', $product->thumbnail_url) }}"
                                       placeholder="https://example.com/image.jpg">
                                @error('thumbnail_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <div id="image-preview" class="mt-2" style="display: none;">
                                    <label class="form-label small text-muted">Xem trước ảnh mới:</label>
                                    <img id="preview-img" src="" class="img-thumbnail" style="max-height: 150px;">
                                </div>
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
                                   {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Đang bán</label>
                        </div>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_featured" value="0">
                            <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1"
                                   {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
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

                <!-- Delete Variant Confirmation Modal -->
                <div class="modal fade" id="deleteVariantModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Xác nhận xóa</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>Bạn có chắc chắn muốn xóa biến thể này?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger" id="confirmDeleteVariantBtn">OK</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Lưu thay đổi
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Hủy</a>
                </div>
            </form>
        </div>
        
        <div class="col-lg-4">
            <!-- Preview Card -->
            <div class="card">
                <div class="card-header">Xem trước</div>
                <div class="card-body text-center">
                    <img src="{{ $product->thumbnail_url ?: 'https://placehold.co/200x200/e2e8f0/64748b?text=No+Image' }}" 
                         alt="{{ $product->name }}" class="rounded mb-3" style="max-width: 200px;">
                    <h6>{{ $product->name }}</h6>
                    <p class="text-primary fw-bold mb-1">{{ number_format($product->price, 0, ',', '.') }}đ</p>
                    @if($product->original_price)
                        <p class="text-muted text-decoration-line-through small">
                            {{ number_format($product->original_price, 0, ',', '.') }}đ
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    const preview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
        document.getElementById('thumbnail_url').value = '';
    } else {
        preview.style.display = 'none';
    }
}

// Variant management
let variantIndex = 0;

function addVariantRow(data = {}) {
    const tbody = document.getElementById('variants-body');
    const row = document.createElement('tr');
    const idField = data.id ? `<input type="hidden" name="variants[${variantIndex}][id]" value="${data.id}">` : '';
    
    row.innerHTML = `
        ${idField}
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
                   value="${data.stock_quantity || 0}" readonly>
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
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDeleteVariant(this)">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    variantIndex++;
}

let variantRowToDelete = null;
const deleteVariantModal = new bootstrap.Modal(document.getElementById('deleteVariantModal'));

function confirmDeleteVariant(btn) {
    variantRowToDelete = btn.closest('tr');
    deleteVariantModal.show();
}

document.getElementById('confirmDeleteVariantBtn').addEventListener('click', function() {
    if (variantRowToDelete) {
        variantRowToDelete.remove();
        variantRowToDelete = null;
    }
    deleteVariantModal.hide();
});

// Load existing variants on page load
document.addEventListener('DOMContentLoaded', function() {
    const existingVariants = @json($product->variants ?? []);
    existingVariants.forEach(variant => {
        addVariantRow({
            id: variant.id,
            size: variant.size,
            color: variant.color,
            color_code: variant.color_code,
            stock_quantity: variant.stock_quantity,
            price_adjustment: variant.price_adjustment,
            sku: variant.sku
        });
    });
});
</script>
@endpush


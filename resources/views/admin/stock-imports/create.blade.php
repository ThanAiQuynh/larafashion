@extends('layouts.admin')

@section('title', 'Tạo phiếu nhập kho')
@section('page-title', 'Tạo phiếu nhập kho')

@section('content')
    <form action="{{ route('admin.stock-imports.store') }}" method="POST" id="importForm">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">Thông tin phiếu nhập</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Mã phiếu</label>
                                <input type="text" class="form-control" value="{{ $code }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="supplier_id" class="form-label">Nhà cung cấp <span class="text-danger">*</span></label>
                                <select class="form-select @error('supplier_id') is-invalid @enderror" 
                                        id="supplier_id" name="supplier_id" required>
                                    <option value="">-- Chọn nhà cung cấp --</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="import_date" class="form-label">Ngày nhập <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('import_date') is-invalid @enderror" 
                                       id="import_date" name="import_date" value="{{ old('import_date', date('Y-m-d')) }}" required>
                                @error('import_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="notes" class="form-label">Ghi chú</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Chi tiết sản phẩm nhập</span>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addItemRow()">
                            <i class="bi bi-plus-lg me-1"></i>Thêm sản phẩm
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="items-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th style="width: 150px;">Biến thể</th>
                                        <th style="width: 100px;">Số lượng</th>
                                        <th style="width: 150px;">Đơn giá nhập</th>
                                        <th style="width: 140px;">Thành tiền</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="items-body">
                                    <!-- Items will be added here -->
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="4" class="text-end">Tổng cộng:</th>
                                        <th class="text-end" id="grand-total">0đ</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">Thao tác</div>
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-check-lg me-1"></i> Lưu phiếu nhập
                        </button>
                        <a href="{{ route('admin.stock-imports.index') }}" class="btn btn-outline-secondary w-100">
                            Hủy
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
const products = @json($products);
let itemIndex = 0;

function addItemRow() {
    const tbody = document.getElementById('items-body');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <select class="form-select form-select-sm product-select" name="items[${itemIndex}][product_id]" required onchange="loadVariants(this, ${itemIndex})">
                <option value="">-- Chọn sản phẩm --</option>
                ${products.map(p => `<option value="${p.id}" data-variants='${JSON.stringify(p.variants)}'>${p.name} (${p.sku})</option>`).join('')}
            </select>
        </td>
        <td>
            <select class="form-select form-select-sm variant-select" name="items[${itemIndex}][variant_id]" id="variant-${itemIndex}">
                <option value="">Không có</option>
            </select>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm quantity-input" 
                   name="items[${itemIndex}][quantity]" min="1" value="1" required onchange="calculateRowTotal(this)">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm price-input" 
                   name="items[${itemIndex}][unit_price]" min="0" value="0" required onchange="calculateRowTotal(this)">
        </td>
        <td class="text-end">
            <span class="row-total fw-medium">0đ</span>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItemRow(this)">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    itemIndex++;
}

function loadVariants(select, index) {
    const option = select.options[select.selectedIndex];
    const variants = JSON.parse(option.dataset.variants || '[]');
    const variantSelect = document.getElementById(`variant-${index}`);
    
    variantSelect.innerHTML = '<option value="">Không có</option>';
    variants.forEach(v => {
        let label = [];
        if (v.size) label.push('Size: ' + v.size);
        if (v.color) label.push('Màu: ' + v.color);
        if (label.length > 0) {
            variantSelect.innerHTML += `<option value="${v.id}">${label.join(', ')}</option>`;
        }
    });
}

function removeItemRow(btn) {
    btn.closest('tr').remove();
    calculateGrandTotal();
}

function calculateRowTotal(input) {
    const row = input.closest('tr');
    const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
    const price = parseFloat(row.querySelector('.price-input').value) || 0;
    const total = quantity * price;
    row.querySelector('.row-total').textContent = new Intl.NumberFormat('vi-VN').format(total) + 'đ';
    calculateGrandTotal();
}

function calculateGrandTotal() {
    let grand = 0;
    document.querySelectorAll('#items-body tr').forEach(row => {
        const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        grand += quantity * price;
    });
    document.getElementById('grand-total').textContent = new Intl.NumberFormat('vi-VN').format(grand) + 'đ';
}

// Add first row on load
document.addEventListener('DOMContentLoaded', function() {
    addItemRow();
});
</script>
@endpush

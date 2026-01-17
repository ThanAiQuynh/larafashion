@extends('layouts.admin')

@section('title', 'Voucher khuyến mãi')
@section('page-title', 'Quản lý Voucher')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#voucherModal" onclick="openCreateModal()">
            <i class="bi bi-plus-lg me-1"></i> Thêm Voucher
        </button>
        <span class="text-muted">Tổng: {{ $vouchers->count() }} voucher</span>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($vouchers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mã voucher</th>
                                <th>Tên</th>
                                <th>Giảm giá</th>
                                <th class="text-center">Đã dùng</th>
                                <th>Thời gian</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center" style="width: 120px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vouchers as $voucher)
                                <tr data-id="{{ $voucher->id }}">
                                    <td><code class="fs-6">{{ $voucher->code }}</code></td>
                                    <td>{{ $voucher->name }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $voucher->getValueDisplay() }}</span>
                                        @if($voucher->type === 'percentage' && $voucher->max_discount)
                                            <br><small class="text-muted">Tối đa: {{ number_format($voucher->max_discount, 0, ',', '.') }}đ</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{ $voucher->usage_count }}/{{ $voucher->usage_limit ?? '∞' }}
                                    </td>
                                    <td class="time-cell"
                                        data-start="{{ $voucher->start_date->format('Y-m-d\TH:i:s') }}"
                                        data-end="{{ $voucher->end_date->format('Y-m-d\TH:i:s') }}">
                                        <small>
                                            {{ $voucher->start_date->format('d/m/Y H:i') }} - {{ $voucher->end_date->format('d/m/Y H:i') }}
                                        </small>
                                        <span class="time-badge"></span>
                                    </td>
                                    <td class="text-center status-cell" 
                                        data-start="{{ $voucher->start_date->format('Y-m-d\TH:i:s') }}"
                                        data-end="{{ $voucher->end_date->format('Y-m-d\TH:i:s') }}"
                                        data-active="{{ $voucher->is_active ? '1' : '0' }}">
                                        @if(now() > $voucher->end_date)
                                            <span class="badge bg-secondary">Hết hạn</span>
                                        @elseif(now() < $voucher->start_date)
                                            <span class="badge bg-info">Chưa bắt đầu</span>
                                        @elseif(!$voucher->is_active)
                                            <span class="badge bg-warning text-dark">Ngừng</span>
                                        @else
                                            <span class="badge bg-success">Hoạt động</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="openEditModal({{ $voucher->id }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmDelete({{ $voucher->id }}, '{{ $voucher->code }}')"
                                                    {{ $voucher->usage_count > 0 ? 'disabled' : '' }}>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-ticket-perforated display-4 text-muted"></i>
                    <p class="mt-3 text-muted">Chưa có voucher nào</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#voucherModal" onclick="openCreateModal()">
                        <i class="bi bi-plus-lg me-1"></i> Thêm voucher đầu tiên
                    </button>
                </div>
            @endif
        </div>
        <div class="card-footer d-flex justify-content-center border-top-0 bg-transparent py-3">
            {{ $vouchers->links() }}
        </div>
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

    <!-- Voucher Modal -->
    <div class="modal fade" id="voucherModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Thêm Voucher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="voucherForm">
                    <div class="modal-body">
                        <input type="hidden" id="voucher_id" name="voucher_id">
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="code" class="form-label">Mã voucher <span class="text-danger">*</span></label>
                                <input type="text" class="form-control text-uppercase" id="code" name="code" required maxlength="50">
                                <small class="text-muted">VD: SALE10, FREESHIP</small>
                            </div>
                            <div class="col-md-8">
                                <label for="name" class="form-label">Tên hiển thị <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-4">
                                <label for="type" class="form-label">Loại giảm giá <span class="text-danger">*</span></label>
                                <select class="form-select" id="type" name="type" required onchange="toggleMaxDiscount()">
                                    <option value="percentage">Giảm theo %</option>
                                    <option value="fixed_amount">Giảm số tiền cố định</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="value" class="form-label">Giá trị <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="value" name="value" required min="0" step="0.01">
                                    <span class="input-group-text" id="value-unit">%</span>
                                </div>
                            </div>
                            <div class="col-md-4" id="max-discount-group">
                                <label for="max_discount" class="form-label">Giảm tối đa</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="max_discount" name="max_discount" min="0">
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="min_order_value" class="form-label">Đơn tối thiểu</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="min_order_value" name="min_order_value" min="0" value="0">
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="usage_limit" class="form-label">Giới hạn lượt dùng</label>
                                <input type="number" class="form-control" id="usage_limit" name="usage_limit" min="0" placeholder="Không giới hạn">
                            </div>
                            <div class="col-md-4">
                                <label for="usage_per_user" class="form-label">Lượt/người dùng</label>
                                <input type="number" class="form-control" id="usage_per_user" name="usage_per_user" min="1" value="1">
                            </div>
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="start_date" name="start_date" required>
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="end_date" name="end_date" required>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
                                    <label class="form-check-label" for="is_active">Kích hoạt voucher</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            Lưu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận xóa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa voucher "<strong id="deleteName"></strong>"?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Notification Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <i class="bi bi-check-circle-fill text-success display-4 mb-3"></i>
                    <h5 class="mb-2">Thành công!</h5>
                    <p class="text-muted mb-0" id="successMessage">Thao tác đã hoàn thành.</p>
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0">
                    <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
const voucherModal = new bootstrap.Modal(document.getElementById('voucherModal'));
const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
const successModal = new bootstrap.Modal(document.getElementById('successModal'));
let deleteId = null;

function showSuccess(message) {
    document.getElementById('successMessage').textContent = message;
    successModal.show();
}

function toggleMaxDiscount() {
    const type = document.getElementById('type').value;
    const unit = document.getElementById('value-unit');
    const maxGroup = document.getElementById('max-discount-group');
    
    if (type === 'percentage') {
        unit.textContent = '%';
        maxGroup.style.display = 'block';
    } else {
        unit.textContent = 'đ';
        maxGroup.style.display = 'none';
    }
}

function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Thêm Voucher';
    document.getElementById('voucherForm').reset();
    document.getElementById('voucher_id').value = '';
    document.getElementById('is_active').checked = true;
    toggleMaxDiscount();
}

function openEditModal(id) {
    document.getElementById('modalTitle').textContent = 'Sửa Voucher';
    
    fetch(`/admin/vouchers/${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const v = data.voucher;
                document.getElementById('voucher_id').value = v.id;
                document.getElementById('code').value = v.code;
                document.getElementById('name').value = v.name;
                document.getElementById('type').value = v.type;
                document.getElementById('value').value = v.value;
                document.getElementById('max_discount').value = v.max_discount || '';
                document.getElementById('min_order_value').value = v.min_order_value || 0;
                document.getElementById('usage_limit').value = v.usage_limit || '';
                document.getElementById('usage_per_user').value = v.usage_per_user || 1;
                document.getElementById('start_date').value = v.start_date.slice(0, 16);
                document.getElementById('end_date').value = v.end_date.slice(0, 16);
                document.getElementById('is_active').checked = v.is_active;
                toggleMaxDiscount();
                voucherModal.show();
            }
        });
}

function confirmDelete(id, code) {
    deleteId = id;
    document.getElementById('deleteName').textContent = code;
    deleteModal.show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (!deleteId) return;
    
    fetch(`/admin/vouchers/${deleteId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        deleteModal.hide();
        if (data.success) {
            document.querySelector(`tr[data-id="${deleteId}"]`).remove();
            showSuccess(data.message);
        } else {
            showSuccess(data.message);
        }
    });
});

document.getElementById('voucherForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const id = document.getElementById('voucher_id').value;
    const isEdit = !!id;
    const url = isEdit ? `/admin/vouchers/${id}` : '/admin/vouchers';
    const method = isEdit ? 'PUT' : 'POST';
    
    const saveBtn = document.getElementById('saveBtn');
    const spinner = saveBtn.querySelector('.spinner-border');
    saveBtn.disabled = true;
    spinner.classList.remove('d-none');
    
    const formData = {
        code: document.getElementById('code').value,
        name: document.getElementById('name').value,
        type: document.getElementById('type').value,
        value: document.getElementById('value').value,
        max_discount: document.getElementById('max_discount').value || null,
        min_order_value: document.getElementById('min_order_value').value || 0,
        usage_limit: document.getElementById('usage_limit').value || null,
        usage_per_user: document.getElementById('usage_per_user').value || 1,
        start_date: document.getElementById('start_date').value,
        end_date: document.getElementById('end_date').value,
        is_active: document.getElementById('is_active').checked
    };
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(res => res.json())
    .then(data => {
        saveBtn.disabled = false;
        spinner.classList.add('d-none');
        
        if (data.success) {
            voucherModal.hide();
            showSuccess(data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showSuccess(data.message || 'Có lỗi xảy ra');
        }
    })
    .catch(err => {
        saveBtn.disabled = false;
        spinner.classList.add('d-none');
        showSuccess('Có lỗi xảy ra');
    });
});

// Real-time voucher status update
function updateVoucherStatuses() {
    const now = new Date();
    
    // Update status column
    document.querySelectorAll('.status-cell').forEach(cell => {
        const startDate = new Date(cell.dataset.start);
        const endDate = new Date(cell.dataset.end);
        const isActive = cell.dataset.active === '1';
        
        let badge = '';
        if (now > endDate) {
            badge = '<span class="badge bg-secondary">Hết hạn</span>';
        } else if (now < startDate) {
            badge = '<span class="badge bg-info">Chưa bắt đầu</span>';
        } else if (!isActive) {
            badge = '<span class="badge bg-warning text-dark">Ngừng</span>';
        } else {
            badge = '<span class="badge bg-success">Hoạt động</span>';
        }
        
        cell.innerHTML = badge;
    });
    
    // Update time column badges
    document.querySelectorAll('.time-cell').forEach(cell => {
        const startDate = new Date(cell.dataset.start);
        const endDate = new Date(cell.dataset.end);
        const badgeSpan = cell.querySelector('.time-badge');
        
        if (!badgeSpan) return;
        
        let badge = '';
        if (now > endDate) {
            badge = '<br><span class="badge bg-secondary">Đã hết hạn</span>';
        } else if (now < startDate) {
            const diff = startDate - now;
            const hours = Math.floor(diff / (1000 * 60 * 60));
            const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            if (hours < 24) {
                badge = `<br><span class="badge bg-info">Còn ${hours}h ${mins}m</span>`;
            }
        } else {
            const diff = endDate - now;
            const hours = Math.floor(diff / (1000 * 60 * 60));
            const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            if (hours < 24) {
                badge = `<br><span class="badge bg-warning text-dark">Hết hạn sau ${hours}h ${mins}m</span>`;
            }
        }
        
        badgeSpan.innerHTML = badge;
    });
}

// Update every second
setInterval(updateVoucherStatuses, 1000);
// Initial update
updateVoucherStatuses();
</script>
@endpush

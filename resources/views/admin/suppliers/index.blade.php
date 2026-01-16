@extends('layouts.admin')

@section('title', 'Nhà cung cấp')
@section('page-title', 'Quản lý nhà cung cấp')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#supplierModal"
            onclick="openCreateModal()">
            <i class="bi bi-plus-lg me-1"></i> Thêm nhà cung cấp
        </button>
        <span class="text-muted">Tổng: {{ $suppliers->count() }} nhà cung cấp</span>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($suppliers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mã NCC</th>
                                <th>Tên nhà cung cấp</th>
                                <th>Liên hệ</th>
                                <th class="text-center">Số phiếu nhập</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center" style="width: 120px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($suppliers as $supplier)
                                <tr data-id="{{ $supplier->id }}">
                                    <td><code>{{ $supplier->code }}</code></td>
                                    <td>
                                        <div class="fw-medium">{{ $supplier->name }}</div>
                                        @if($supplier->contact_person)
                                            <small class="text-muted">{{ $supplier->contact_person }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($supplier->phone)
                                            <div><i class="bi bi-telephone me-1"></i>{{ $supplier->phone }}</div>
                                        @endif
                                        @if($supplier->email)
                                            <small class="text-muted"><i class="bi bi-envelope me-1"></i>{{ $supplier->email }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $supplier->stock_imports_count }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $supplier->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $supplier->is_active ? 'Hoạt động' : 'Ngừng' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                onclick="openEditModal({{ $supplier->id }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="confirmDelete({{ $supplier->id }}, '{{ addslashes($supplier->name) }}')">
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
                    <i class="bi bi-truck display-4 text-muted"></i>
                    <p class="mt-3 text-muted">Chưa có nhà cung cấp nào</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#supplierModal"
                        onclick="openCreateModal()">
                        <i class="bi bi-plus-lg me-1"></i> Thêm nhà cung cấp đầu tiên
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Supplier Modal -->
    <div class="modal fade" id="supplierModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Thêm nhà cung cấp</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="supplierForm">
                    <div class="modal-body">
                        <input type="hidden" id="supplier_id" name="supplier_id">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="code" class="form-label">Mã NCC <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="code" name="code" required>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Tên nhà cung cấp <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Số điện thoại</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                            <div class="col-12">
                                <label for="contact_person" class="form-label">Người liên hệ</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person">
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label">Địa chỉ</label>
                                <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                            </div>
                            <div class="col-12">
                                <label for="notes" class="form-label">Ghi chú</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
                                    <label class="form-check-label" for="is_active">Đang hoạt động</label>
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
                    <p>Bạn có chắc chắn muốn xóa nhà cung cấp "<strong id="deleteName"></strong>"?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">OK</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const supplierModal = new bootstrap.Modal(document.getElementById('supplierModal'));
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        let deleteId = null;

        function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Thêm nhà cung cấp';
            document.getElementById('supplierForm').reset();
            document.getElementById('supplier_id').value = '';
            document.getElementById('is_active').checked = true;
        }

        function openEditModal(id) {
            document.getElementById('modalTitle').textContent = 'Sửa nhà cung cấp';

            fetch(`/admin/suppliers/${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const s = data.supplier;
                        document.getElementById('supplier_id').value = s.id;
                        document.getElementById('code').value = s.code;
                        document.getElementById('name').value = s.name;
                        document.getElementById('phone').value = s.phone || '';
                        document.getElementById('email').value = s.email || '';
                        document.getElementById('contact_person').value = s.contact_person || '';
                        document.getElementById('address').value = s.address || '';
                        document.getElementById('notes').value = s.notes || '';
                        document.getElementById('is_active').checked = s.is_active;
                        supplierModal.show();
                    }
                });
        }

        function confirmDelete(id, name) {
            deleteId = id;
            document.getElementById('deleteName').textContent = name;
            deleteModal.show();
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
            if (!deleteId) return;

            fetch(`/admin/suppliers/${deleteId}`, {
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
                        showToast('success', data.message);
                    } else {
                        showToast('error', data.message);
                    }
                });
        });

        document.getElementById('supplierForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const id = document.getElementById('supplier_id').value;
            const isEdit = !!id;
            const url = isEdit ? `/admin/suppliers/${id}` : '/admin/suppliers';
            const method = isEdit ? 'PUT' : 'POST';

            const saveBtn = document.getElementById('saveBtn');
            const spinner = saveBtn.querySelector('.spinner-border');
            saveBtn.disabled = true;
            spinner.classList.remove('d-none');

            const formData = {
                code: document.getElementById('code').value,
                name: document.getElementById('name').value,
                phone: document.getElementById('phone').value,
                email: document.getElementById('email').value,
                contact_person: document.getElementById('contact_person').value,
                address: document.getElementById('address').value,
                notes: document.getElementById('notes').value,
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
                        supplierModal.hide();
                        showToast('success', data.message);
                        location.reload();
                    } else {
                        showToast('error', data.message || 'Có lỗi xảy ra');
                    }
                })
                .catch(err => {
                    saveBtn.disabled = false;
                    spinner.classList.add('d-none');
                    showToast('error', 'Có lỗi xảy ra');
                });
        });

        function showToast(type, message) {
            alert(message); // TODO: Replace with proper toast
        }
    </script>
@endpush
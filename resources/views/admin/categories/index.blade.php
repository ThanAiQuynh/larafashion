@extends('layouts.admin')

@section('title', 'Quản lý danh mục')
@section('page-title', 'Danh mục sản phẩm')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Danh sách danh mục</h5>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#categoryModal"
                onclick="openCreateModal()">
                <i class="bi bi-plus-lg me-1"></i>Thêm danh mục
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="categoriesTable">
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
                            <tr data-id="{{ $category->id }}">
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
                                    <button type="button" class="btn btn-sm btn-outline-primary me-1"
                                        onclick="openEditModal({{ $category->id }})" title="Sửa">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDelete({{ $category->id }}, '{{ $category->name }}')" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
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

    <!-- Category Modal (Create/Edit) -->
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Thêm danh mục</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="categoryForm">
                    <div class="modal-body">
                        <input type="hidden" id="categoryId" name="id">

                        <div class="mb-3">
                            <label for="categoryName" class="form-label">Tên danh mục <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="categoryName" name="name" required>
                            <div class="invalid-feedback" id="nameError"></div>
                        </div>

                        <div class="mb-3">
                            <label for="categoryParent" class="form-label">Danh mục cha</label>
                            <select class="form-select" id="categoryParent" name="parent_id">
                                <option value="">-- Không có --</option>
                                @foreach($parentCategories as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="parentError"></div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="categoryActive" name="is_active"
                                    value="1" checked>
                                <label class="form-check-label" for="categoryActive">Kích hoạt danh mục</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <span class="spinner-border spinner-border-sm d-none" id="submitSpinner"></span>
                            <span id="submitText">Lưu danh mục</span>
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
                    <p>Bạn có chắc muốn xóa danh mục "<strong id="deleteCategoryName"></strong>"?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <span class="spinner-border spinner-border-sm d-none" id="deleteSpinner"></span>
                        <span id="deleteText">Xóa</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        let editingId = null;
        let deletingId = null;

        // CSRF Token for AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function openCreateModal() {
            editingId = null;
            document.getElementById('categoryModalLabel').textContent = 'Thêm danh mục';
            document.getElementById('submitText').textContent = 'Lưu danh mục';
            document.getElementById('categoryForm').reset();
            document.getElementById('categoryActive').checked = true;
            clearFormErrors();
        }

        async function openEditModal(id) {
            editingId = id;
            document.getElementById('categoryModalLabel').textContent = 'Sửa danh mục';
            document.getElementById('submitText').textContent = 'Cập nhật';
            clearFormErrors();

            try {
                const response = await fetch(`/admin/categories/${id}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();

                if (data.success) {
                    document.getElementById('categoryId').value = data.category.id;
                    document.getElementById('categoryName').value = data.category.name;
                    document.getElementById('categoryParent').value = data.category.parent_id || '';
                    document.getElementById('categoryActive').checked = data.category.is_active;
                    categoryModal.show();
                }
            } catch (error) {
                showToast('Không thể tải dữ liệu danh mục', 'error');
            }
        }

        function confirmDelete(id, name) {
            deletingId = id;
            document.getElementById('deleteCategoryName').textContent = name;
            deleteModal.show();
        }

        function clearFormErrors() {
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        }

        function showFormError(field, message) {
            const input = document.getElementById('category' + field.charAt(0).toUpperCase() + field.slice(1));
            const feedback = document.getElementById(field + 'Error');
            if (input) input.classList.add('is-invalid');
            if (feedback) feedback.textContent = message;
        }

        function setLoading(btn, spinner, text, loading) {
            if (loading) {
                btn.disabled = true;
                spinner.classList.remove('d-none');
                text.classList.add('d-none');
            } else {
                btn.disabled = false;
                spinner.classList.add('d-none');
                text.classList.remove('d-none');
            }
        }

        // Form submit handler
        document.getElementById('categoryForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            clearFormErrors();

            const submitBtn = document.getElementById('submitBtn');
            const spinner = document.getElementById('submitSpinner');
            const text = document.getElementById('submitText');
            setLoading(submitBtn, spinner, text, true);

            const formData = new FormData(this);
            formData.append('is_active', document.getElementById('categoryActive').checked ? '1' : '0');

            const url = editingId
                ? `/admin/categories/${editingId}`
                : '/admin/categories';

            const method = editingId ? 'PUT' : 'POST';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name: formData.get('name'),
                        parent_id: formData.get('parent_id') || null,
                        is_active: document.getElementById('categoryActive').checked
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showToast(data.message, 'success');
                    categoryModal.hide();
                    location.reload(); // Reload to update table
                } else {
                    showToast(data.message || 'Có lỗi xảy ra', 'error');
                }
            } catch (error) {
                showToast('Có lỗi xảy ra, vui lòng thử lại', 'error');
            } finally {
                setLoading(submitBtn, spinner, text, false);
            }
        });

        // Delete handler
        document.getElementById('confirmDeleteBtn').addEventListener('click', async function () {
            const btn = this;
            const spinner = document.getElementById('deleteSpinner');
            const text = document.getElementById('deleteText');
            setLoading(btn, spinner, text, true);

            try {
                const response = await fetch(`/admin/categories/${deletingId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showToast(data.message, 'success');
                    deleteModal.hide();
                    document.querySelector(`tr[data-id="${deletingId}"]`)?.remove();
                } else {
                    showToast(data.message || 'Không thể xóa danh mục', 'error');
                }
            } catch (error) {
                showToast('Có lỗi xảy ra, vui lòng thử lại', 'error');
            } finally {
                setLoading(btn, spinner, text, false);
            }
        });
    </script>
@endpush
@extends('layouts.admin')

@section('title', 'Quản lý thương hiệu')
@section('page-title', 'Thương hiệu sản phẩm')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Danh sách thương hiệu</h5>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#brandModal"
                onclick="openCreateModal()">
                <i class="bi bi-plus-lg me-1"></i>Thêm thương hiệu
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="brandsTable">
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
                            <tr data-id="{{ $brand->id }}">
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
                                    <button type="button" class="btn btn-sm btn-outline-primary me-1"
                                        onclick="openEditModal({{ $brand->id }})" title="Sửa">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDelete({{ $brand->id }}, '{{ $brand->name }}')" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Chưa có thương hiệu nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Brand Modal (Create/Edit) -->
    <div class="modal fade" id="brandModal" tabindex="-1" aria-labelledby="brandModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="brandModalLabel">Thêm thương hiệu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="brandForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" id="brandId" name="id">

                        <div class="mb-3">
                            <label for="brandName" class="form-label">Tên thương hiệu <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="brandName" name="name" required>
                            <div class="invalid-feedback" id="nameError"></div>
                        </div>

                        <div class="mb-3">
                            <label for="brandLogo" class="form-label">Logo thương hiệu</label>
                            <div id="currentLogo" class="mb-2" style="display: none;">
                                <img id="currentLogoImg" src="" class="rounded border" style="max-height: 60px;">
                                <small class="text-muted d-block">Logo hiện tại</small>
                            </div>
                            <input type="file" class="form-control" id="brandLogo" name="logo" accept="image/*"
                                onchange="previewLogo(this)">
                            <div class="form-text">Chấp nhận: JPG, PNG, GIF. Tối đa 2MB.</div>
                            <div id="logoPreview" class="mt-2" style="display: none;">
                                <img id="logoPreviewImg" src="" class="rounded" style="max-height: 60px;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <span class="spinner-border spinner-border-sm d-none" id="submitSpinner"></span>
                            <span id="submitText">Lưu thương hiệu</span>
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
                    <p>Bạn có chắc muốn xóa thương hiệu "<strong id="deleteBrandName"></strong>"?</p>
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
        const brandModal = new bootstrap.Modal(document.getElementById('brandModal'));
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        let editingId = null;
        let deletingId = null;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function previewLogo(input) {
            const preview = document.getElementById('logoPreview');
            const previewImg = document.getElementById('logoPreviewImg');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }

        function openCreateModal() {
            editingId = null;
            document.getElementById('brandModalLabel').textContent = 'Thêm thương hiệu';
            document.getElementById('submitText').textContent = 'Lưu thương hiệu';
            document.getElementById('brandForm').reset();
            document.getElementById('currentLogo').style.display = 'none';
            document.getElementById('logoPreview').style.display = 'none';
            clearFormErrors();
        }

        async function openEditModal(id) {
            editingId = id;
            document.getElementById('brandModalLabel').textContent = 'Sửa thương hiệu';
            document.getElementById('submitText').textContent = 'Cập nhật';
            document.getElementById('logoPreview').style.display = 'none';
            clearFormErrors();

            try {
                const response = await fetch(`/admin/brands/${id}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();

                if (data.success) {
                    document.getElementById('brandId').value = data.brand.id;
                    document.getElementById('brandName').value = data.brand.name;

                    if (data.brand.logo_url) {
                        document.getElementById('currentLogoImg').src = data.brand.logo_url;
                        document.getElementById('currentLogo').style.display = 'block';
                    } else {
                        document.getElementById('currentLogo').style.display = 'none';
                    }

                    brandModal.show();
                }
            } catch (error) {
                showToast('Không thể tải dữ liệu thương hiệu', 'error');
            }
        }

        function confirmDelete(id, name) {
            deletingId = id;
            document.getElementById('deleteBrandName').textContent = name;
            deleteModal.show();
        }

        function clearFormErrors() {
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
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

        // Form submit handler - using FormData for file upload
        document.getElementById('brandForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            clearFormErrors();

            const submitBtn = document.getElementById('submitBtn');
            const spinner = document.getElementById('submitSpinner');
            const text = document.getElementById('submitText');
            setLoading(submitBtn, spinner, text, true);

            const formData = new FormData(this);

            const url = editingId
                ? `/admin/brands/${editingId}`
                : '/admin/brands';

            // For PUT request with file, we need to use POST with _method
            if (editingId) {
                formData.append('_method', 'PUT');
            }

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showToast(data.message, 'success');
                    brandModal.hide();
                    location.reload();
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
                const response = await fetch(`/admin/brands/${deletingId}`, {
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
                    showToast(data.message || 'Không thể xóa thương hiệu', 'error');
                }
            } catch (error) {
                showToast('Có lỗi xảy ra, vui lòng thử lại', 'error');
            } finally {
                setLoading(btn, spinner, text, false);
            }
        });
    </script>
@endpush
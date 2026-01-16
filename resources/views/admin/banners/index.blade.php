@extends('layouts.admin')

@section('title', 'Quản lý Banner')
@section('page-title', 'Quản lý Banner')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-muted mb-0">Quản lý banner hiển thị trên trang chủ</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bannerModal"
            onclick="openCreateModal()">
            <i class="bi bi-plus-lg me-1"></i> Thêm banner
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="bannersTable">
                    <thead class="table-light">
                        <tr>
                            <th width="80">Vị trí</th>
                            <th width="150">Ảnh</th>
                            <th>Tiêu đề</th>
                            <th>Link</th>
                            <th width="100">Trạng thái</th>
                            <th width="150">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($banners as $banner)
                            <tr data-id="{{ $banner->id }}">
                                <td><span class="badge bg-secondary">{{ $banner->position }}</span></td>
                                <td>
                                    <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="rounded"
                                        style="height: 60px; width: 100px; object-fit: cover;">
                                </td>
                                <td class="fw-medium">{{ $banner->title }}</td>
                                <td>
                                    @if($banner->link_url)
                                        <a href="{{ $banner->link_url }}" target="_blank" class="text-truncate d-block"
                                            style="max-width: 200px;">
                                            {{ $banner->link_url }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button"
                                        class="btn btn-sm {{ $banner->is_active ? 'btn-success' : 'btn-secondary' }}"
                                        onclick="toggleStatus({{ $banner->id }})">
                                        {{ $banner->is_active ? 'Hiện' : 'Ẩn' }}
                                    </button>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="openEditModal({{ $banner->id }})" title="Sửa">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDelete({{ $banner->id }}, '{{ $banner->title }}')" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-image display-4"></i>
                                    <p class="mt-2">Chưa có banner nào</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $banners->links() }}
    </div>

    <!-- Banner Modal (Create/Edit) -->
    <div class="modal fade" id="bannerModal" tabindex="-1" aria-labelledby="bannerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bannerModalLabel">Thêm banner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="bannerForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" id="bannerId" name="id">

                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="bannerTitle" class="form-label">Tiêu đề <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="bannerTitle" name="title" required>
                            </div>
                            <div class="col-md-4">
                                <label for="bannerPosition" class="form-label">Vị trí <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="bannerPosition" name="position" value="0"
                                    min="0" required>
                                <small class="text-muted">Số nhỏ hiển thị trước</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Ảnh banner <span class="text-danger"
                                        id="imageRequired">*</span></label>
                                <div id="currentImage" class="mb-2" style="display: none;">
                                    <img id="currentImageImg" src="" class="rounded" style="max-height: 120px;">
                                    <small class="text-muted d-block">Ảnh hiện tại</small>
                                </div>
                                <input type="file" class="form-control" id="bannerImage" name="image_file" accept="image/*"
                                    onchange="previewImage(this)">
                                <small class="text-muted">Kích thước khuyến nghị: 1920x600px. Tối đa 5MB</small>
                                <div id="imagePreview" class="mt-2" style="display: none;">
                                    <img id="imagePreviewImg" src="" class="img-fluid rounded" style="max-height: 150px;">
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="bannerLink" class="form-label">Link URL</label>
                                <input type="url" class="form-control" id="bannerLink" name="link_url"
                                    placeholder="https://example.com">
                                <small class="text-muted">Để trống nếu không muốn liên kết</small>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="bannerActive" name="is_active"
                                        value="1" checked>
                                    <label class="form-check-label" for="bannerActive">Hiển thị banner</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <span class="spinner-border spinner-border-sm d-none" id="submitSpinner"></span>
                            <span id="submitText">Thêm banner</span>
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
                    <p>Bạn có chắc muốn xóa banner "<strong id="deleteBannerName"></strong>"?</p>
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
        const bannerModal = new bootstrap.Modal(document.getElementById('bannerModal'));
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        let editingId = null;
        let deletingId = null;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('imagePreviewImg');

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
            document.getElementById('bannerModalLabel').textContent = 'Thêm banner';
            document.getElementById('submitText').textContent = 'Thêm banner';
            document.getElementById('bannerForm').reset();
            document.getElementById('bannerActive').checked = true;
            document.getElementById('currentImage').style.display = 'none';
            document.getElementById('imagePreview').style.display = 'none';
            document.getElementById('imageRequired').style.display = 'inline';
            document.getElementById('bannerImage').required = true;
        }

        async function openEditModal(id) {
            editingId = id;
            document.getElementById('bannerModalLabel').textContent = 'Sửa banner';
            document.getElementById('submitText').textContent = 'Lưu thay đổi';
            document.getElementById('imagePreview').style.display = 'none';
            document.getElementById('imageRequired').style.display = 'none';
            document.getElementById('bannerImage').required = false;

            try {
                const response = await fetch(`/admin/banners/${id}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();

                if (data.success) {
                    document.getElementById('bannerId').value = data.banner.id;
                    document.getElementById('bannerTitle').value = data.banner.title;
                    document.getElementById('bannerPosition').value = data.banner.position;
                    document.getElementById('bannerLink').value = data.banner.link_url || '';
                    document.getElementById('bannerActive').checked = data.banner.is_active;

                    if (data.banner.image_url) {
                        document.getElementById('currentImageImg').src = data.banner.image_url;
                        document.getElementById('currentImage').style.display = 'block';
                    } else {
                        document.getElementById('currentImage').style.display = 'none';
                    }

                    bannerModal.show();
                }
            } catch (error) {
                showToast('Không thể tải dữ liệu banner', 'error');
            }
        }

        function confirmDelete(id, name) {
            deletingId = id;
            document.getElementById('deleteBannerName').textContent = name;
            deleteModal.show();
        }

        async function toggleStatus(id) {
            try {
                const response = await fetch(`/admin/banners/${id}/toggle-status`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showToast(data.message, 'success');
                    location.reload();
                } else {
                    showToast(data.message || 'Có lỗi xảy ra', 'error');
                }
            } catch (error) {
                showToast('Có lỗi xảy ra', 'error');
            }
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
        document.getElementById('bannerForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            const spinner = document.getElementById('submitSpinner');
            const text = document.getElementById('submitText');
            setLoading(submitBtn, spinner, text, true);

            const formData = new FormData(this);
            formData.append('is_active', document.getElementById('bannerActive').checked ? '1' : '0');

            const url = editingId
                ? `/admin/banners/${editingId}`
                : '/admin/banners';

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
                    bannerModal.hide();
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
                const response = await fetch(`/admin/banners/${deletingId}`, {
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
                    showToast(data.message || 'Không thể xóa banner', 'error');
                }
            } catch (error) {
                showToast('Có lỗi xảy ra, vui lòng thử lại', 'error');
            } finally {
                setLoading(btn, spinner, text, false);
            }
        });
    </script>
@endpush
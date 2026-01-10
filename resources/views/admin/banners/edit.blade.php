@extends('layouts.admin')

@section('title', 'Sửa Banner')
@section('page-title', 'Sửa Banner')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $banner->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="position" class="form-label">Vị trí <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('position') is-invalid @enderror" 
                                       id="position" name="position" value="{{ old('position', $banner->position) }}" min="0" required>
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Ảnh hiện tại</label>
                                <div class="mb-2">
                                    <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" 
                                         class="rounded" style="max-height: 150px;">
                                </div>
                                <label for="image_file" class="form-label">Đổi ảnh mới</label>
                                <input type="file" class="form-control @error('image_file') is-invalid @enderror" 
                                       id="image_file" name="image_file" accept="image/*" onchange="previewImage(this)">
                                @error('image_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Để trống nếu không muốn đổi ảnh</small>
                                <div id="image-preview" class="mt-3" style="display: none;">
                                    <img id="preview-img" src="" class="img-fluid rounded" style="max-height: 200px;">
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="link_url" class="form-label">Link URL</label>
                                <input type="url" class="form-control @error('link_url') is-invalid @enderror" 
                                       id="link_url" name="link_url" value="{{ old('link_url', $banner->link_url) }}"
                                       placeholder="https://example.com">
                                @error('link_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" 
                                           {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Hiển thị banner</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Lưu thay đổi
                    </button>
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary">Hủy</a>
                </div>
            </form>
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
    } else {
        preview.style.display = 'none';
    }
}
</script>
@endpush

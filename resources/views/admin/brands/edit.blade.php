@extends('layouts.admin')

@section('title', 'Chỉnh sửa thương hiệu')
@section('page-title', 'Chỉnh sửa: ' . $brand->name)

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.brands.update', $brand) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên thương hiệu</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $brand->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="logo" class="form-label">Logo thương hiệu</label>
                        @if($brand->logo_url)
                            <div class="mb-2">
                                <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" class="rounded border" style="max-height: 80px;">
                            </div>
                        @endif
                        <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo" accept="image/*">
                        @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Để trống nếu không muốn thay đổi logo.</div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Cập nhật thương hiệu</button>
                        <a href="{{ route('admin.brands.index') }}" class="btn btn-light ms-2">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


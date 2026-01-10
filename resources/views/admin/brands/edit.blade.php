@extends('layouts.admin')

@section('title', 'Chỉnh sửa thương hiệu')
@section('page-title', 'Chỉnh sửa: ' . $brand->name)

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.brands.update', $brand) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên thương hiệu</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $brand->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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

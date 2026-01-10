@extends('layouts.app')

@section('title', 'Đổi mật khẩu - LaraFashion')

@section('content')
<div class="container py-5">
    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-lg-3">
            @include('account.partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm mb-4">{{ session('error') }}</div>
            @endif

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Đổi mật khẩu</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('account.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Mật khẩu hiện tại</label>
                                <input type="password" name="current_password" class="form-control form-control-lg @error('current_password') is-invalid @enderror">
                                @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12"></div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Mật khẩu mới</label>
                                <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" placeholder="Ít nhất 6 ký tự">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Xác nhận mật khẩu mới</label>
                                <input type="password" name="password_confirmation" class="form-control form-control-lg">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5">Cập nhật mật khẩu</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

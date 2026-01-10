@extends('layouts.app')

@section('title', 'Đăng ký - LaraFashion')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold mb-1">Tạo tài khoản</h3>
                        <p class="text-muted">Đăng ký để mua sắm dễ dàng hơn!</p>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger border-0 mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('register') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-medium">Họ và tên</label>
                            <input type="text" name="name" class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" placeholder="Nguyễn Văn A" autofocus>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium">Email</label>
                            <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" placeholder="email@example.com">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium">Số điện thoại <span class="text-muted fw-normal">(không bắt buộc)</span></label>
                            <input type="text" name="phone_number" class="form-control form-control-lg @error('phone_number') is-invalid @enderror" 
                                   value="{{ old('phone_number') }}" placeholder="09xxxxxxxx">
                            @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium">Mật khẩu</label>
                            <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                   placeholder="Ít nhất 6 ký tự">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium">Xác nhận mật khẩu</label>
                            <input type="password" name="password_confirmation" class="form-control form-control-lg" 
                                   placeholder="Nhập lại mật khẩu">
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill py-3 fw-bold mb-4">
                            Đăng ký
                        </button>

                        <div class="text-center">
                            <p class="text-muted mb-0">
                                Đã có tài khoản? 
                                <a href="{{ route('login') }}" class="text-decoration-none fw-medium">Đăng nhập</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Đăng nhập - LaraFashion')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold mb-1">Đăng nhập</h3>
                        <p class="text-muted">Chào mừng bạn quay trở lại!</p>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger border-0 mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success border-0 mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-medium">Email</label>
                            <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" placeholder="email@example.com" autofocus>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label fw-medium">Mật khẩu</label>
                                <a href="#" class="text-decoration-none small">Quên mật khẩu?</a>
                            </div>
                            <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                   placeholder="••••••••">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill py-3 fw-bold mb-4">
                            Đăng nhập
                        </button>

                        <div class="text-center">
                            <p class="text-muted mb-0">
                                Chưa có tài khoản? 
                                <a href="{{ route('register') }}" class="text-decoration-none fw-medium">Đăng ký ngay</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

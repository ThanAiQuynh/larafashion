@extends('layouts.app')

@section('title', 'Thông tin cá nhân - LaraFashion')

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

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Thông tin cá nhân</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('account.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Họ và tên</label>
                                <input type="text" name="name" class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                       value="{{ old('name', Auth::user()->name) }}">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Email</label>
                                <input type="email" class="form-control form-control-lg" value="{{ Auth::user()->email }}" disabled>
                                <small class="text-muted">Email không thể thay đổi.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Số điện thoại</label>
                                <input type="text" name="phone_number" class="form-control form-control-lg @error('phone_number') is-invalid @enderror" 
                                       value="{{ old('phone_number', Auth::user()->phone_number) }}" placeholder="09xxxxxxxx">
                                @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5">Lưu thay đổi</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

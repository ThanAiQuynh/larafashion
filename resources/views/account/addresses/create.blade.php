@extends('layouts.app')

@section('title', 'Thêm địa chỉ mới - LaraFashion')

@section('content')
<div class="container py-5">
    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-lg-3">
            @include('account.partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('account.addresses.index') }}" class="btn btn-light rounded-circle me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h4 class="fw-bold mb-0">Thêm địa chỉ mới</h4>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('account.addresses.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Họ tên người nhận</label>
                                <input type="text" name="recipient_name" class="form-control @error('recipient_name') is-invalid @enderror" value="{{ old('recipient_name', Auth::user()->name) }}">
                                @error('recipient_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Số điện thoại</label>
                                <input type="tel" name="recipient_phone" class="form-control @error('recipient_phone') is-invalid @enderror" value="{{ old('recipient_phone', Auth::user()->phone_number) }}">
                                @error('recipient_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-medium">Địa chỉ cụ thể (Số nhà, tên đường)</label>
                                <input type="text" name="address_line" class="form-control @error('address_line') is-invalid @enderror" value="{{ old('address_line') }}" placeholder="Ví dụ: 123 Đường Nguyễn Huệ">
                                @error('address_line') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Tỉnh/Thành phố</label>
                                <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}">
                                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Quận/Huyện</label>
                                <input type="text" name="district" class="form-control @error('district') is-invalid @enderror" value="{{ old('district') }}">
                                @error('district') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Phường/Xã</label>
                                <input type="text" name="ward" class="form-control @error('ward') is-invalid @enderror" value="{{ old('ward') }}">
                                @error('ward') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_default" value="1" id="is_default" {{ old('is_default') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default">
                                        Đặt làm địa chỉ mặc định
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">Lưu địa chỉ</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

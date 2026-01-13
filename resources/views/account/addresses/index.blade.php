@extends('layouts.app')

@section('title', 'Sổ địa chỉ - LaraFashion')

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

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">Sổ địa chỉ</h4>
                <a href="{{ route('account.addresses.create') }}" class="btn btn-primary rounded-pill">
                    <i class="bi bi-plus-lg me-2"></i>Thêm địa chỉ mới
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    @if($addresses->count() > 0)
                        <div class="list-group list-group-flush rounded-4">
                            @foreach($addresses as $address)
                                <div class="list-group-item p-4 border-0 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <h6 class="fw-bold mb-0">{{ $address->recipient_name }}</h6>
                                                @if($address->is_default)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Mặc định</span>
                                                @endif
                                            </div>
                                            <p class="text-muted mb-1">{{ $address->recipient_phone }}</p>
                                            <p class="mb-0 text-muted">{{ $address->address_line }}, {{ $address->ward }}, {{ $address->district }}, {{ $address->city }}</p>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm rounded-circle" type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                                @if(!$address->is_default)
                                                    <li>
                                                        <form action="{{ route('account.addresses.default', $address) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item">Thiết lập mặc định</button>
                                                        </form>
                                                    </li>
                                                @endif
                                                <li><a class="dropdown-item" href="{{ route('account.addresses.edit', $address) }}">Chỉnh sửa</a></li>
                                                @if(!$address->is_default)
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('account.addresses.destroy', $address) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa địa chỉ này?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">Xóa địa chỉ</button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-geo-alt display-1 text-muted opacity-25"></i>
                            <p class="text-muted mt-3">Bạn chưa lưu địa chỉ nào.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

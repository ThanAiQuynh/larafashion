<div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px;">
    <div class="card-body p-4">
        <div class="text-center mb-4">
            <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                <i class="bi bi-person-fill text-white fs-1"></i>
            </div>
            <h5 class="fw-bold mb-0">{{ Auth::user()->name }}</h5>
            <small class="text-muted">{{ Auth::user()->email }}</small>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link px-3 py-2 rounded {{ request()->routeIs('account.index') ? 'bg-primary text-white' : 'text-dark' }}" href="{{ route('account.index') }}">
                <i class="bi bi-grid me-2"></i> Tổng quan
            </a>
            <a class="nav-link px-3 py-2 rounded {{ request()->routeIs('account.orders*') ? 'bg-primary text-white' : 'text-dark' }}" href="{{ route('account.orders') }}">
                <i class="bi bi-bag me-2"></i> Đơn hàng
            </a>
            <a class="nav-link px-3 py-2 rounded {{ request()->routeIs('account.addresses*') ? 'bg-primary text-white' : 'text-dark' }}" href="{{ route('account.addresses.index') }}">
                <i class="bi bi-geo-alt me-2"></i> Sổ địa chỉ
            </a>
            <a class="nav-link px-3 py-2 rounded {{ request()->routeIs('account.profile') ? 'bg-primary text-white' : 'text-dark' }}" href="{{ route('account.profile') }}">
                <i class="bi bi-person me-2"></i> Thông tin
            </a>
            <a class="nav-link px-3 py-2 rounded {{ request()->routeIs('account.password') ? 'bg-primary text-white' : 'text-dark' }}" href="{{ route('account.password') }}">
                <i class="bi bi-shield-lock me-2"></i> Đổi mật khẩu
            </a>
            <hr>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-link px-3 py-2 text-danger w-100 text-start border-0 bg-transparent">
                    <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                </button>
            </form>
        </nav>
    </div>
</div>

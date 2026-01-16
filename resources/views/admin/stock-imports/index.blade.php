@extends('layouts.admin')

@section('title', 'Phiếu nhập kho')
@section('page-title', 'Quản lý phiếu nhập kho')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('admin.stock-imports.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Tạo phiếu nhập
        </a>
        <span class="text-muted">Tổng: {{ $stockImports->total() }} phiếu</span>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($stockImports->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mã phiếu</th>
                                <th>Nhà cung cấp</th>
                                <th>Ngày nhập</th>
                                <th class="text-center">Số mặt hàng</th>
                                <th class="text-end">Tổng tiền</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center" style="width: 150px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stockImports as $import)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.stock-imports.show', $import) }}" class="fw-medium">
                                            <code>{{ $import->code }}</code>
                                        </a>
                                    </td>
                                    <td>{{ $import->supplier->name }}</td>
                                    <td>{{ $import->import_date->format('d/m/Y') }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $import->items_count }}</span>
                                    </td>
                                    <td class="text-end fw-medium">
                                        {{ number_format($import->total_amount, 0, ',', '.') }}đ
                                    </td>
                                    <td class="text-center">
                                        @if($import->status === 'pending')
                                            <span class="badge bg-warning text-dark">Chờ xử lý</span>
                                        @elseif($import->status === 'completed')
                                            <span class="badge bg-success">Đã nhập kho</span>
                                        @else
                                            <span class="badge bg-secondary">Đã hủy</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ route('admin.stock-imports.show', $import) }}"
                                                class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($import->isPending())
                                                <form action="{{ route('admin.stock-imports.confirm', $import) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Xác nhận nhập kho"
                                                        onclick="return confirm('Xác nhận nhập kho?')">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.stock-imports.cancel', $import) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hủy phiếu"
                                                        onclick="return confirm('Hủy phiếu nhập này?')">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    {{ $stockImports->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-box-arrow-in-down display-4 text-muted"></i>
                    <p class="mt-3 text-muted">Chưa có phiếu nhập nào</p>
                    <a href="{{ route('admin.stock-imports.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Tạo phiếu nhập đầu tiên
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
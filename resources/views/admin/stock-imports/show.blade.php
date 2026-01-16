@extends('layouts.admin')

@section('title', 'Chi tiết phiếu nhập')
@section('page-title', 'Chi tiết phiếu nhập')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('admin.stock-imports.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>

        @if($stockImport->isPending())
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmImportModal">
                    <i class="bi bi-check-lg me-1"></i> Xác nhận nhập kho
                </button>
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelImportModal">
                    <i class="bi bi-x-lg me-1"></i> Hủy phiếu
                </button>

                <!-- Confirm Import Modal -->
                <div class="modal fade" id="confirmImportModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Xác nhận nhập kho</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Bạn có chắc chắn muốn xác nhận phiếu nhập <strong>{{ $stockImport->code }}</strong>?</p>
                                <p class="text-danger mb-0"><i class="bi bi-exclamation-triangle me-1"></i> Sau khi xác nhận, số
                                    lượng sản phẩm sẽ được cộng vào kho và không thể hoàn tác.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                <form action="{{ route('admin.stock-imports.confirm', $stockImport) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success">Đồng ý xác nhận</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cancel Import Modal -->
                <div class="modal fade" id="cancelImportModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Hủy phiếu nhập</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Bạn có chắc chắn muốn hủy phiếu nhập <strong>{{ $stockImport->code }}</strong>?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                <form action="{{ route('admin.stock-imports.cancel', $stockImport) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">Đồng ý hủy</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Thông tin phiếu nhập</span>
                    @if($stockImport->status === 'pending')
                        <span class="badge bg-warning text-dark">Chờ xử lý</span>
                    @elseif($stockImport->status === 'completed')
                        <span class="badge bg-success">Đã nhập kho</span>
                    @else
                        <span class="badge bg-secondary">Đã hủy</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th class="ps-0" style="width: 140px;">Mã phiếu:</th>
                                    <td><code class="fs-5">{{ $stockImport->code }}</code></td>
                                </tr>
                                <tr>
                                    <th class="ps-0">Nhà cung cấp:</th>
                                    <td>{{ $stockImport->supplier->name }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-0">Ngày nhập:</th>
                                    <td>{{ $stockImport->import_date->format('d/m/Y') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th style="width: 140px;">Người tạo:</th>
                                    <td>{{ $stockImport->creator->name }}</td>
                                </tr>
                                <tr>
                                    <th>Ngày tạo:</th>
                                    <td>{{ $stockImport->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Ghi chú:</th>
                                    <td>{{ $stockImport->notes ?: '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Chi tiết sản phẩm nhập</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Sản phẩm</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end">Đơn giá</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stockImport->items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="fw-medium">{{ $item->product->name }}</div>
                                            @if($item->variant)
                                                <small class="text-muted">
                                                    @if($item->variant->size) Size: {{ $item->variant->size }} @endif
                                                    @if($item->variant->color) | Màu: {{ $item->variant->color }} @endif
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ number_format($item->quantity) }}</td>
                                        <td class="text-end">{{ number_format($item->unit_price, 0, ',', '.') }}đ</td>
                                        <td class="text-end fw-medium">{{ number_format($item->total_price, 0, ',', '.') }}đ
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="4" class="text-end">Tổng cộng:</th>
                                    <th class="text-end fs-5 text-primary">
                                        {{ number_format($stockImport->total_amount, 0, ',', '.') }}đ
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">Tóm tắt</div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Số mặt hàng:</span>
                        <span class="fw-medium">{{ $stockImport->items->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tổng số lượng:</span>
                        <span class="fw-medium">{{ number_format($stockImport->items->sum('quantity')) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fs-5">Tổng tiền:</span>
                        <span
                            class="fs-5 fw-bold text-primary">{{ number_format($stockImport->total_amount, 0, ',', '.') }}đ</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
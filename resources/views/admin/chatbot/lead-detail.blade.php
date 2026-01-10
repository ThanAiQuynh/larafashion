@extends('layouts.admin')

@section('title', 'Chi tiết Lead #' . $lead->id)
@section('page-title', 'Chi tiết Lead')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <!-- Lead Info -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Thông tin Lead #{{ $lead->id }}</span>
                    @if($lead->is_processed)
                        <span class="badge badge-processed">
                            <i class="bi bi-check-lg me-1"></i>Đã xử lý
                        </span>
                    @else
                        <span class="badge badge-unprocessed">
                            <i class="bi bi-clock me-1"></i>Chưa xử lý
                        </span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Tên khách hàng</label>
                            <div class="fw-medium fs-5">{{ $lead->customer_name ?: 'Chưa có tên' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Số điện thoại</label>
                            <div class="fw-medium fs-5">
                                @if($lead->customer_phone)
                                    <a href="tel:{{ $lead->customer_phone }}" class="text-decoration-none">
                                        <i class="bi bi-telephone me-1"></i>{{ $lead->customer_phone }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Email</label>
                            <div>
                                @if($lead->customer_email)
                                    <a href="mailto:{{ $lead->customer_email }}" class="text-decoration-none">
                                        <i class="bi bi-envelope me-1"></i>{{ $lead->customer_email }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Session ID</label>
                            <div>
                                <code>{{ $lead->tudongchat_session_id ?: '-' }}</code>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted">Nhu cầu / Câu hỏi</label>
                            <div class="bg-light p-3 rounded">
                                {{ $lead->intent ?: 'Không có thông tin' }}
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted">Thời gian nhận</label>
                            <div>
                                <i class="bi bi-calendar me-1"></i>
                                {{ $lead->created_at?->format('d/m/Y H:i:s') }}
                                <span class="text-muted">({{ $lead->created_at?->diffForHumans() }})</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Raw Data -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-code-slash me-2"></i>Dữ liệu gốc (Raw Payload)
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded mb-0" style="max-height: 400px; overflow: auto;"><code>{{ json_encode($lead->raw_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Actions -->
            <div class="card mb-4">
                <div class="card-header">Thao tác</div>
                <div class="card-body d-grid gap-2">
                    @if($lead->customer_phone)
                        <a href="tel:{{ $lead->customer_phone }}" class="btn btn-success">
                            <i class="bi bi-telephone me-2"></i>Gọi điện
                        </a>
                    @endif
                    
                    @if(!$lead->is_processed)
                        <form action="{{ route('admin.chatbot.leads.process', $lead) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-check-lg me-2"></i>Đánh dấu đã xử lý
                            </button>
                        </form>
                    @else
                        <button class="btn btn-secondary" disabled>
                            <i class="bi bi-check-circle me-2"></i>Đã xử lý
                        </button>
                    @endif
                    
                    <hr>
                    
                    <form action="{{ route('admin.chatbot.leads.delete', $lead) }}" method="POST"
                          onsubmit="return confirm('Bạn có chắc muốn xóa lead này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-trash me-2"></i>Xóa lead
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Back -->
            <a href="{{ route('admin.chatbot.leads') }}" class="btn btn-outline-secondary w-100">
                <i class="bi bi-arrow-left me-2"></i>Quay lại danh sách
            </a>
        </div>
    </div>
@endsection

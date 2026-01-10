@extends('layouts.admin')

@section('title', 'Chatbot Leads')
@section('page-title', 'Quản lý Leads từ Chatbot')

@section('content')
    <!-- Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stats-card">
                <div class="icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <div class="value">{{ $stats['total'] }}</div>
                    <div class="label">Tổng leads</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <div class="value">{{ $stats['unprocessed'] }}</div>
                    <div class="label">Chưa xử lý</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div>
                    <div class="value">{{ $stats['today'] }}</div>
                    <div class="label">Hôm nay</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.chatbot.leads') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Tìm theo SĐT, tên, email..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="unprocessed" {{ request('status') == 'unprocessed' ? 'selected' : '' }}>Chưa xử lý</option>
                        <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Đã xử lý</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Lọc
                    </button>
                </div>
                @if(request()->hasAny(['search', 'status']))
                    <div class="col-md-2">
                        <a href="{{ route('admin.chatbot.leads') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-x-lg me-1"></i> Xóa bộ lọc
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Leads Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Danh sách Leads</span>
            <span class="text-muted">{{ $leads->total() }} kết quả</span>
        </div>
        <div class="card-body p-0">
            @if($leads->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Khách hàng</th>
                                <th>Liên hệ</th>
                                <th>Nhu cầu</th>
                                <th>Thời gian</th>
                                <th>Trạng thái</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leads as $lead)
                                <tr>
                                    <td><strong>#{{ $lead->id }}</strong></td>
                                    <td>
                                        <div class="fw-medium">{{ $lead->customer_name ?: 'Chưa có tên' }}</div>
                                        @if($lead->tudongchat_session_id)
                                            <small class="text-muted">Session: {{ Str::limit($lead->tudongchat_session_id, 20) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($lead->customer_phone)
                                            <div><i class="bi bi-telephone me-1"></i>{{ $lead->customer_phone }}</div>
                                        @endif
                                        @if($lead->customer_email)
                                            <div><i class="bi bi-envelope me-1"></i>{{ $lead->customer_email }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span title="{{ $lead->intent }}">
                                            {{ Str::limit($lead->intent, 50) ?: '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>{{ $lead->created_at?->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $lead->created_at?->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        @if($lead->is_processed)
                                            <span class="badge badge-processed">
                                                <i class="bi bi-check-lg me-1"></i>Đã xử lý
                                            </span>
                                        @else
                                            <span class="badge badge-unprocessed">
                                                <i class="bi bi-clock me-1"></i>Chưa xử lý
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.chatbot.leads.show', $lead) }}" 
                                               class="btn btn-outline-primary" title="Xem chi tiết">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if(!$lead->is_processed)
                                                <form action="{{ route('admin.chatbot.leads.process', $lead) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-outline-success" title="Đánh dấu đã xử lý">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('admin.chatbot.leads.delete', $lead) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Bạn có chắc muốn xóa lead này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Xóa">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="card-footer">
                    {{ $leads->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-4 text-muted"></i>
                    <p class="mt-3 text-muted">Chưa có lead nào</p>
                </div>
            @endif
        </div>
    </div>
@endsection

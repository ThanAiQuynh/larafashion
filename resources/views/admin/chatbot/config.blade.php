@extends('layouts.admin')

@section('title', 'Cấu hình Chatbot')
@section('page-title', 'Cấu hình Tudongchat')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <!-- Config Form -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-gear me-2"></i>Cài đặt tích hợp Tudongchat
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.chatbot.config.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Active Toggle -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" class="form-check-input" id="is_active" 
                                       name="is_active" value="1" style="width: 3rem; height: 1.5rem;"
                                       {{ $config->is_active ? 'checked' : '' }}>
                                <label class="form-check-label ms-2" for="is_active">
                                    <strong>Kích hoạt Chatbot</strong>
                                </label>
                            </div>
                            <small class="text-muted">Bật/tắt hiển thị chatbot trên website</small>
                        </div>
                        
                        <!-- Embed Script -->
                        <div class="mb-4">
                            <label for="script_code" class="form-label">
                                <strong>Mã nhúng (Embed Script)</strong>
                            </label>
                            <textarea class="form-control font-monospace" id="script_code" name="script_code" 
                                      rows="8" placeholder="Dán mã script từ Tudongchat.com vào đây...">{{ $config->script_code }}</textarea>
                            <small class="text-muted">
                                Lấy mã script từ dashboard Tudongchat của bạn. 
                                <a href="https://app.tudongchat.com" target="_blank">Truy cập Tudongchat →</a>
                            </small>
                        </div>
                        
                        <!-- Webhook URL -->
                        <div class="mb-4">
                            <label class="form-label"><strong>Webhook URL</strong></label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-light" 
                                       value="{{ route('api.webhook.tudongchat') }}" readonly id="webhook-url">
                                <button type="button" class="btn btn-outline-secondary" onclick="copyWebhookUrl()">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                            <small class="text-muted">Cấu hình URL này trong phần Webhook của Tudongchat</small>
                        </div>
                        
                        <!-- Webhook Secret -->
                        <div class="mb-4">
                            <label for="webhook_secret" class="form-label">
                                <strong>Webhook Secret Key</strong>
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control font-monospace" id="webhook_secret" 
                                       name="webhook_secret" value="{{ $config->webhook_secret }}" 
                                       placeholder="Khóa bảo mật cho webhook (tùy chọn)">
                                <button type="button" class="btn btn-outline-secondary" 
                                        onclick="document.getElementById('generate-secret-form').submit()">
                                    <i class="bi bi-arrow-repeat me-1"></i>Tạo mới
                                </button>
                            </div>
                            <small class="text-muted">
                                Sử dụng header <code>X-Webhook-Secret</code> để bảo vệ webhook
                            </small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>Lưu cấu hình
                        </button>
                    </form>
                    
                    <!-- Hidden form for generate secret -->
                    <form id="generate-secret-form" action="{{ route('admin.chatbot.config.generate-secret') }}" 
                          method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card mb-4">
                <div class="card-header">Trạng thái</div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        @if($config->is_active && $config->script_code)
                            <span class="badge bg-success fs-6">
                                <i class="bi bi-check-circle me-1"></i>Đang hoạt động
                            </span>
                        @else
                            <span class="badge bg-secondary fs-6">
                                <i class="bi bi-pause-circle me-1"></i>Chưa kích hoạt
                            </span>
                        @endif
                    </div>
                    
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            @if($config->script_code)
                                <i class="bi bi-check-circle text-success me-2"></i>
                            @else
                                <i class="bi bi-x-circle text-danger me-2"></i>
                            @endif
                            Mã nhúng
                        </li>
                        <li class="mb-2">
                            @if($config->webhook_secret)
                                <i class="bi bi-check-circle text-success me-2"></i>
                            @else
                                <i class="bi bi-exclamation-circle text-warning me-2"></i>
                            @endif
                            Webhook Secret
                        </li>
                        <li>
                            @if($config->is_active)
                                <i class="bi bi-check-circle text-success me-2"></i>
                            @else
                                <i class="bi bi-x-circle text-danger me-2"></i>
                            @endif
                            Kích hoạt
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Help Card -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-question-circle me-2"></i>Hướng dẫn
                </div>
                <div class="card-body">
                    <ol class="mb-0">
                        <li class="mb-2">Đăng ký tài khoản tại <a href="https://app.tudongchat.com" target="_blank">Tudongchat.com</a></li>
                        <li class="mb-2">Tạo chatbot và train với data sản phẩm</li>
                        <li class="mb-2">Copy mã embed script và dán vào form bên trái</li>
                        <li class="mb-2">Cấu hình Webhook URL trong Tudongchat</li>
                        <li>Bật "Kích hoạt Chatbot" để hiển thị trên website</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function copyWebhookUrl() {
    const input = document.getElementById('webhook-url');
    input.select();
    document.execCommand('copy');
    alert('Đã copy Webhook URL!');
}
</script>
@endpush

@extends('layouts.admin')

@section('title', 'Trợ lý AI')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="bi bi-robot me-2"></i>Trợ lý AI
            </h4>
            <p class="text-muted mb-0">Hỏi đáp thông minh về dữ liệu kinh doanh</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-chat-dots-fill fs-4 me-2"></i>
                        <div>
                            <h6 class="mb-0 fw-bold">Chat với Trợ lý AI</h6>
                            <small class="opacity-75">Hỏi về doanh thu, đơn hàng, sản phẩm...</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="chat-messages" class="p-4"
                        style="min-height: 400px; max-height: 500px; overflow-y: auto; background: #f8f9fa;">
                        <!-- Welcome Message -->
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <span class="badge bg-primary rounded-circle p-2">
                                    <i class="bi bi-robot"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="bg-white rounded-3 p-3 shadow-sm">
                                    <p class="mb-2">Xin chào! 👋 Tôi là trợ lý AI của LaraFashion.</p>
                                    <p class="mb-2">Tôi có thể giúp bạn trả lời các câu hỏi về:</p>
                                    <ul class="mb-2 ps-3">
                                        <li>💰 Doanh thu (hôm nay, tuần này, tháng này)</li>
                                        <li>📦 Đơn hàng (số lượng, trạng thái)</li>
                                        <li>🏆 Sản phẩm bán chạy & Tồn kho</li>
                                        <li>🎫 Voucher & Khuyến mãi</li>
                                        <li>🏷️ Thương hiệu & Danh mục</li>
                                        <li>🤝 Nhà cung cấp & Đơn nhập hàng</li>
                                    </ul>
                                    <p class="mb-0 text-muted small">Hãy nhập câu hỏi của bạn bên dưới!</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 border-top bg-white">
                        <form id="chat-form" class="d-flex gap-2">
                            @csrf
                            <input type="text" id="question-input" class="form-control form-control-lg"
                                placeholder="Nhập câu hỏi của bạn... (VD: Doanh thu hôm nay là bao nhiêu?)"
                                autocomplete="off" required>
                            <button type="submit" class="btn btn-primary btn-lg px-4" id="send-btn">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-lightbulb me-2"></i>Gợi ý câu hỏi</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary text-start suggestion-btn"
                            data-question="Doanh thu hôm nay là bao nhiêu?">
                            💰 Doanh thu hôm nay là bao nhiêu?
                        </button>
                        <button class="btn btn-outline-primary text-start suggestion-btn"
                            data-question="Doanh thu tháng này là bao nhiêu?">
                            📊 Doanh thu tháng này là bao nhiêu?
                        </button>
                        <button class="btn btn-outline-primary text-start suggestion-btn"
                            data-question="Có bao nhiêu đơn hàng mới hôm nay?">
                            📦 Có bao nhiêu đơn hàng mới hôm nay?
                        </button>
                        <button class="btn btn-outline-primary text-start suggestion-btn"
                            data-question="Đơn hàng chờ xử lý là bao nhiêu?">
                            ⏳ Đơn hàng chờ xử lý là bao nhiêu?
                        </button>
                        <button class="btn btn-outline-primary text-start suggestion-btn"
                            data-question="Sản phẩm bán chạy nhất tuần này là gì?">
                            🏆 Sản phẩm bán chạy nhất tuần này?
                        </button>
                        <button class="btn btn-outline-primary text-start suggestion-btn"
                            data-question="Tình trạng tồn kho như thế nào?">
                            📦 Tình trạng tồn kho như thế nào?
                        </button>
                        <button class="btn btn-outline-primary text-start suggestion-btn"
                            data-question="Có bao nhiêu khách hàng mới tháng này?">
                            👥 Khách hàng mới tháng này?
                        </button>
                        <button class="btn btn-outline-primary text-start suggestion-btn"
                            data-question="Cho tôi biết về các voucher giảm giá?">
                            🎫 Thông tin voucher giảm giá?
                        </button>
                        <button class="btn btn-outline-primary text-start suggestion-btn"
                            data-question="Các thương hiệu có trong cửa hàng?">
                            🏷️ Các thương hiệu trong cửa hàng?
                        </button>
                        <button class="btn btn-outline-primary text-start suggestion-btn"
                            data-question="Danh mục sản phẩm hiện có?">
                            📁 Các danh mục sản phẩm?
                        </button>
                        <button class="btn btn-outline-primary text-start suggestion-btn"
                            data-question="Thông tin nhà cung cấp?">
                            🤝 Thông tin nhà cung cấp?
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <style>
            #chat-messages::-webkit-scrollbar {
                width: 6px;
            }

            #chat-messages::-webkit-scrollbar-track {
                background: #f1f1f1;
            }

            #chat-messages::-webkit-scrollbar-thumb {
                background: #c1c1c1;
                border-radius: 3px;
            }

            .suggestion-btn:hover {
                transform: translateX(5px);
                transition: transform 0.2s;
            }

            .typing-indicator span {
                width: 8px;
                height: 8px;
                background-color: #6c757d;
                border-radius: 50%;
                display: inline-block;
                margin: 0 2px;
                animation: typing 1s infinite;
            }

            .typing-indicator span:nth-child(2) {
                animation-delay: 0.2s;
            }

            .typing-indicator span:nth-child(3) {
                animation-delay: 0.4s;
            }

            @keyframes typing {

                0%,
                100% {
                    opacity: 0.3;
                    transform: translateY(0);
                }

                50% {
                    opacity: 1;
                    transform: translateY(-3px);
                }
            }
        </style>
@endsection

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('chat-form');
                const input = document.getElementById('question-input');
                const messages = document.getElementById('chat-messages');
                const sendBtn = document.getElementById('send-btn');

                // Handle form submission
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const question = input.value.trim();
                    if (!question) return;

                    sendMessage(question);
                    input.value = '';
                });

                // Handle suggestion buttons
                document.querySelectorAll('.suggestion-btn').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const question = this.dataset.question;
                        input.value = question;
                        sendMessage(question);
                        input.value = '';
                    });
                });

                function sendMessage(question) {
                    // Add user message
                    addMessage(question, 'user');

                    // Show typing indicator
                    const typingId = showTyping();

                    // Disable input
                    input.disabled = true;
                    sendBtn.disabled = true;

                    // Send request
                    fetch('{{ route("admin.ai-assistant.ask") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ question: question })
                    })
                        .then(response => response.json())
                        .then(data => {
                            hideTyping(typingId);
                            addMessage(data.message, 'bot');
                        })
                        .catch(error => {
                            hideTyping(typingId);
                            addMessage('Xin lỗi, có lỗi xảy ra. Vui lòng thử lại sau.', 'bot');
                        })
                        .finally(() => {
                            input.disabled = false;
                            sendBtn.disabled = false;
                            input.focus();
                        });
                }

                function addMessage(content, type) {
                    const div = document.createElement('div');
                    div.className = 'd-flex mb-3';

                    if (type === 'user') {
                        div.innerHTML = `
                                <div class="flex-grow-1 me-3 text-end">
                                    <div class="bg-primary text-white rounded-3 p-3 d-inline-block" style="max-width: 80%;">
                                        ${escapeHtml(content)}
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="badge bg-secondary rounded-circle p-2">
                                        <i class="bi bi-person"></i>
                                    </span>
                                </div>
                            `;
                    } else {
                        // Parse markdown
                        const htmlContent = marked.parse(content);
                        div.innerHTML = `
                                <div class="flex-shrink-0">
                                    <span class="badge bg-primary rounded-circle p-2">
                                        <i class="bi bi-robot"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="bg-white rounded-3 p-3 shadow-sm" style="max-width: 90%;">
                                        ${htmlContent}
                                    </div>
                                </div>
                            `;
                    }

                    messages.appendChild(div);
                    messages.scrollTop = messages.scrollHeight;
                }

                function showTyping() {
                    const id = 'typing-' + Date.now();
                    const div = document.createElement('div');
                    div.id = id;
                    div.className = 'd-flex mb-3';
                    div.innerHTML = `
                            <div class="flex-shrink-0">
                                <span class="badge bg-primary rounded-circle p-2">
                                    <i class="bi bi-robot"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="bg-white rounded-3 p-3 shadow-sm d-inline-block">
                                    <div class="typing-indicator">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </div>
                                </div>
                            </div>
                        `;
                    messages.appendChild(div);
                    messages.scrollTop = messages.scrollHeight;
                    return id;
                }

                function hideTyping(id) {
                    const element = document.getElementById(id);
                    if (element) element.remove();
                }

                function escapeHtml(text) {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }
            });
        </script>
    @endpush
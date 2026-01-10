<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu - LaraFashion</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .content p {
            color: #64748b;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            background: #6366f1;
            color: white;
            text-decoration: none;
            padding: 14px 30px;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }
        .btn:hover {
            background: #4f46e5;
        }
        .footer {
            background: #f8fafc;
            padding: 20px 30px;
            font-size: 12px;
            color: #94a3b8;
            text-align: center;
        }
        .warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 6px 6px 0;
        }
        .warning p {
            margin: 0;
            color: #92400e;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛍️ LaraFashion</h1>
        </div>
        <div class="content">
            <h2>Đặt lại mật khẩu</h2>
            <p>Xin chào,</p>
            <p>Bạn nhận được email này vì chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.</p>
            
            <div style="text-align: center;">
                <a href="{{ $resetLink }}" class="btn">Đặt lại mật khẩu</a>
            </div>

            <div class="warning">
                <p>⏰ Link này sẽ hết hạn sau <strong>60 phút</strong>.</p>
            </div>

            <p>Nếu bạn không yêu cầu đặt lại mật khẩu, bạn có thể bỏ qua email này.</p>
            
            <p>Nếu nút bên trên không hoạt động, copy và paste link sau vào trình duyệt:</p>
            <p style="word-break: break-all; font-size: 12px; background: #f1f5f9; padding: 10px; border-radius: 4px;">
                {{ $resetLink }}
            </p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} LaraFashion. All rights reserved.</p>
            <p>Email này được gửi tự động, vui lòng không phản hồi.</p>
        </div>
    </div>
</body>
</html>

<?php

return [
    /*
    |--------------------------------------------------------------------------
    | VNPay Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình thông tin VNPay để tích hợp thanh toán.
    | Lấy thông tin từ: https://sandbox.vnpayment.vn/merchantv2/
    |
    */

    // Mã website của merchant trên hệ thống VNPay
    'tmn_code' => env('VNPAY_TMN_CODE', ''),

    // Chuỗi bí mật dùng để hash secure
    'hash_secret' => env('VNPAY_HASH_SECRET', ''),

    // URL thanh toán của VNPay
    // Sandbox: https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
    // Production: https://pay.vnpay.vn/vpcpay.html
    'url' => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),

    // URL nhận kết quả trả về từ VNPay
    'return_url' => env('VNPAY_RETURN_URL', 'http://127.0.0.1:8000/thanh-toan/vnpay-return'),
];

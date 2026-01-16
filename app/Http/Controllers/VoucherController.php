<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VoucherController extends Controller
{
    /**
     * Check voucher validity and return discount amount
     */
    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
            'order_total' => 'required|numeric|min:0',
        ]);

        $code = strtoupper($request->code);
        $orderTotal = $request->order_total;
        $userId = auth()->id();

        $voucher = Voucher::where('code', $code)->first();

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không tồn tại.'
            ], 404);
        }

        if (!$voucher->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá đã hết hạn hoặc hết lượt sử dụng.'
            ], 422);
        }

        if (!$voucher->canBeUsedBy($userId)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã sử dụng mã giảm giá này rồi.'
            ], 422);
        }

        if ($orderTotal < $voucher->min_order_value) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng tối thiểu ' . number_format((float) $voucher->min_order_value, 0, ',', '.') . 'đ để sử dụng mã này.'
            ], 422);
        }

        $discount = $voucher->calculateDiscount($orderTotal);

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công!',
            'data' => [
                'code' => $voucher->code,
                'name' => $voucher->name,
                'discount_amount' => $discount,
                'discount_display' => number_format($discount, 0, ',', '.') . 'đ',
                'new_total' => $orderTotal - $discount,
                'new_total_display' => number_format($orderTotal - $discount, 0, ',', '.') . 'đ'
            ]
        ]);
    }
}

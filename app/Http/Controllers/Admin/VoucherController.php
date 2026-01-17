<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VoucherController extends Controller
{
    /**
     * Display voucher list
     */
    public function index(): View
    {
        $vouchers = Voucher::withCount('usages')->latest()->paginate(10);
        return view('admin.vouchers.index', compact('vouchers'));
    }

    /**
     * Get voucher data for editing (AJAX)
     */
    public function show(Voucher $voucher): JsonResponse
    {
        return response()->json([
            'success' => true,
            'voucher' => $voucher
        ]);
    }

    /**
     * Store new voucher
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed_amount',
            'value' => 'required|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
            'usage_per_user' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['min_order_value'] = $validated['min_order_value'] ?? 0;
        $validated['usage_per_user'] = $validated['usage_per_user'] ?? 1;

        $voucher = Voucher::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm voucher thành công!',
                'voucher' => $voucher
            ]);
        }

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Đã thêm voucher thành công!');
    }

    /**
     * Update voucher
     */
    public function update(Request $request, Voucher $voucher): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code,' . $voucher->id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed_amount',
            'value' => 'required|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
            'usage_per_user' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['min_order_value'] = $validated['min_order_value'] ?? 0;
        $validated['usage_per_user'] = $validated['usage_per_user'] ?? 1;

        $voucher->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật voucher thành công!',
                'voucher' => $voucher->fresh()
            ]);
        }

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Đã cập nhật voucher thành công!');
    }

    /**
     * Delete voucher
     */
    public function destroy(Request $request, Voucher $voucher): RedirectResponse|JsonResponse
    {
        if ($voucher->usage_count > 0) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa voucher đã được sử dụng.'
                ], 422);
            }
            return back()->with('error', 'Không thể xóa voucher đã được sử dụng.');
        }

        $voucher->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa voucher thành công!'
            ]);
        }

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Đã xóa voucher thành công!');
    }
}

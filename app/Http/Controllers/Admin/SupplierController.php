<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    /**
     * Display supplier list
     */
    public function index(): View
    {
        $suppliers = Supplier::withCount('stockImports')->latest()->paginate(10);
        return view('admin.suppliers.index', compact('suppliers'));
    }

    /**
     * Get supplier data for editing (AJAX)
     */
    public function show(Supplier $supplier): JsonResponse
    {
        return response()->json([
            'success' => true,
            'supplier' => $supplier
        ]);
    }

    /**
     * Store new supplier
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:suppliers,code',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $supplier = Supplier::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm nhà cung cấp thành công!',
                'supplier' => $supplier
            ]);
        }

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Đã thêm nhà cung cấp thành công!');
    }

    /**
     * Update supplier
     */
    public function update(Request $request, Supplier $supplier): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:suppliers,code,' . $supplier->id,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $supplier->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật nhà cung cấp thành công!',
                'supplier' => $supplier->fresh()
            ]);
        }

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Đã cập nhật nhà cung cấp thành công!');
    }

    /**
     * Delete supplier
     */
    public function destroy(Request $request, Supplier $supplier): RedirectResponse|JsonResponse
    {
        if ($supplier->stockImports()->count() > 0) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa nhà cung cấp này vì có phiếu nhập liên kết.'
                ], 422);
            }
            return back()->with('error', 'Không thể xóa nhà cung cấp này vì có phiếu nhập liên kết.');
        }

        $supplier->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa nhà cung cấp thành công!'
            ]);
        }

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Đã xóa nhà cung cấp thành công!');
    }
}

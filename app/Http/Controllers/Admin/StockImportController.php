<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockImport;
use App\Models\StockImportItem;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockImportController extends Controller
{
    /**
     * Display list of stock imports
     */
    public function index(): View
    {
        $stockImports = StockImport::with(['supplier', 'creator'])
            ->withCount('items')
            ->latest()
            ->paginate(20);

        return view('admin.stock-imports.index', compact('stockImports'));
    }

    /**
     * Show create form
     */
    public function create(): View
    {
        $suppliers = Supplier::active()->orderBy('name')->get();
        $products = Product::active()->with('variants')->orderBy('name')->get();
        $code = StockImport::generateCode();

        return view('admin.stock-imports.create', compact('suppliers', 'products', 'code'));
    }

    /**
     * Store new stock import
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'import_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Create stock import
            $stockImport = StockImport::create([
                'code' => StockImport::generateCode(),
                'supplier_id' => $validated['supplier_id'],
                'import_date' => $validated['import_date'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
                'created_by' => Auth::guard('admin')->id(),
                'total_amount' => 0,
            ]);

            // Create items and calculate total
            $totalAmount = 0;
            foreach ($validated['items'] as $itemData) {
                $item = $stockImport->items()->create([
                    'product_id' => $itemData['product_id'],
                    'variant_id' => $itemData['variant_id'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                ]);
                $totalAmount += $item->total_price;
            }

            // Update total amount
            $stockImport->update(['total_amount' => $totalAmount]);

            DB::commit();

            return redirect()->route('admin.stock-imports.show', $stockImport)
                ->with('success', 'Đã tạo phiếu nhập thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show stock import details
     */
    public function show(StockImport $stockImport): View
    {
        $stockImport->load(['supplier', 'creator', 'items.product', 'items.variant']);
        return view('admin.stock-imports.show', compact('stockImport'));
    }

    /**
     * Confirm stock import - update stock quantities
     */
    public function confirm(StockImport $stockImport): RedirectResponse|JsonResponse
    {
        if (!$stockImport->isPending()) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phiếu nhập này không thể xác nhận.'
                ], 422);
            }
            return back()->with('error', 'Phiếu nhập này không thể xác nhận.');
        }

        $stockImport->load(['items.product', 'items.variant']);
        $result = $stockImport->confirm();

        if ($result) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã xác nhận phiếu nhập và cập nhật kho thành công!'
                ]);
            }
            return back()->with('success', 'Đã xác nhận phiếu nhập và cập nhật kho thành công!');
        }

        if (request()->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xác nhận phiếu nhập.'
            ], 422);
        }
        return back()->with('error', 'Có lỗi xảy ra khi xác nhận phiếu nhập.');
    }

    /**
     * Cancel stock import
     */
    public function cancel(StockImport $stockImport): RedirectResponse|JsonResponse
    {
        if (!$stockImport->isPending()) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phiếu nhập này không thể hủy.'
                ], 422);
            }
            return back()->with('error', 'Phiếu nhập này không thể hủy.');
        }

        $stockImport->cancel();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã hủy phiếu nhập thành công!'
            ]);
        }
        return back()->with('success', 'Đã hủy phiếu nhập thành công!');
    }

    /**
     * Delete stock import (only pending)
     */
    public function destroy(StockImport $stockImport): RedirectResponse|JsonResponse
    {
        if (!$stockImport->isPending()) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể xóa phiếu nhập đang chờ xử lý.'
                ], 422);
            }
            return back()->with('error', 'Chỉ có thể xóa phiếu nhập đang chờ xử lý.');
        }

        $stockImport->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa phiếu nhập thành công!'
            ]);
        }
        return redirect()->route('admin.stock-imports.index')
            ->with('success', 'Đã xóa phiếu nhập thành công!');
    }

    /**
     * Get variants for a product (AJAX)
     */
    public function getProductVariants(Product $product): JsonResponse
    {
        $variants = $product->variants()->where('is_active', true)->get();
        return response()->json(['variants' => $variants]);
    }
}

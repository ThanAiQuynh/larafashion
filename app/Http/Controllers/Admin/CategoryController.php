<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display category list
     */
    public function index(): View
    {
        $categories = Category::with('parent')->withCount('products')->get();
        $parentCategories = Category::whereNull('parent_id')->get();
        return view('admin.categories.index', compact('categories', 'parentCategories'));
    }

    /**
     * Show create form (for non-AJAX requests)
     */
    public function create(): View
    {
        $parentCategories = Category::whereNull('parent_id')->get();
        return view('admin.categories.create', compact('parentCategories'));
    }

    /**
     * Get category data for editing (AJAX)
     */
    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'success' => true,
            'category' => $category
        ]);
    }

    /**
     * Store new category
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $category = Category::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm danh mục thành công!',
                'category' => $category->load('parent')
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Đã thêm danh mục thành công!');
    }

    /**
     * Show edit form (for non-AJAX requests)
     */
    public function edit(Category $category): View
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->get();
        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update category
     */
    public function update(Request $request, Category $category): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        // Prevent setting itself as parent
        if ($validated['parent_id'] == $category->id) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể thiết lập danh mục này làm cha của chính nó.'
                ], 422);
            }
            return back()->withErrors(['parent_id' => 'Không thể thiết lập danh mục này làm cha của chính nó.']);
        }

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        $category->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật danh mục thành công!',
                'category' => $category->fresh()->load('parent')
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Đã cập nhật danh mục thành công!');
    }

    /**
     * Delete category
     */
    public function destroy(Request $request, Category $category): RedirectResponse|JsonResponse
    {
        // Check if category has children or products
        if ($category->children()->count() > 0) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa danh mục này vì nó có danh mục con.'
                ], 422);
            }
            return back()->with('error', 'Không thể xóa danh mục này vì nó có danh mục con.');
        }

        if ($category->products()->count() > 0) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa danh mục này vì nó có sản phẩm liên kết.'
                ], 422);
            }
            return back()->with('error', 'Không thể xóa danh mục này vì nó có sản phẩm liên kết.');
        }

        $category->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa danh mục thành công!'
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Đã xóa danh mục thành công!');
    }
}


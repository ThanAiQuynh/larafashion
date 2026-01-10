<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
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
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show create form
     */
    public function create(): View
    {
        $parentCategories = Category::whereNull('parent_id')->get();
        return view('admin.categories.create', compact('parentCategories'));
    }

    /**
     * Store new category
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Đã thêm danh mục thành công!');
    }

    /**
     * Show edit form
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
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        // Prevent setting itself as parent
        if ($validated['parent_id'] == $category->id) {
            return back()->withErrors(['parent_id' => 'Không thể thiết lập danh mục này làm cha của chính nó.']);
        }

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Đã cập nhật danh mục thành công!');
    }

    /**
     * Delete category
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Check if category has children or products
        if ($category->children()->count() > 0) {
            return back()->with('error', 'Không thể xóa danh mục này vì nó có danh mục con.');
        }

        if ($category->products()->count() > 0) {
            return back()->with('error', 'Không thể xóa danh mục này vì nó có sản phẩm liên kết.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Đã xóa danh mục thành công!');
    }
}

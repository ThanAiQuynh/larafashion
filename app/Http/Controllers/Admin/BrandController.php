<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BrandController extends Controller
{
    /**
     * Display brand list
     */
    public function index(): View
    {
        $brands = Brand::withCount('products')->get();
        return view('admin.brands.index', compact('brands'));
    }

    /**
     * Show create form
     */
    public function create(): View
    {
        return view('admin.brands.create');
    }

    /**
     * Store new brand
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'logo' => 'nullable|image|mimes:jpeg,png,gif,webp|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $uploadedFile = Cloudinary::upload($request->file('logo')->getRealPath(), [
                'folder' => 'larafashion/brands',
            ]);
            $validated['logo_url'] = $uploadedFile->getSecurePath();
        }

        unset($validated['logo']);
        Brand::create($validated);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Đã thêm thương hiệu thành công!');
    }

    /**
     * Show edit form
     */
    public function edit(Brand $brand): View
    {
        return view('admin.brands.edit', compact('brand'));
    }

    /**
     * Update brand
     */
    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'logo' => 'nullable|image|mimes:jpeg,png,gif,webp|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $uploadedFile = Cloudinary::upload($request->file('logo')->getRealPath(), [
                'folder' => 'larafashion/brands',
            ]);
            $validated['logo_url'] = $uploadedFile->getSecurePath();
        }

        unset($validated['logo']);
        $brand->update($validated);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Đã cập nhật thương hiệu thành công!');
    }

    /**
     * Delete brand
     */
    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->products()->count() > 0) {
            return back()->with('error', 'Không thể xóa thương hiệu này vì nó có sản phẩm liên kết.');
        }

        $brand->delete();

        return redirect()->route('admin.brands.index')
            ->with('success', 'Đã xóa thương hiệu thành công!');
    }
}


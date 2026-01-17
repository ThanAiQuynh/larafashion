<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Services\CloudinaryService;
use Illuminate\Http\JsonResponse;
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
        $brands = Brand::withCount('products')->paginate(10);
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
     * Get brand data for editing (AJAX)
     */
    public function show(Brand $brand): JsonResponse
    {
        return response()->json([
            'success' => true,
            'brand' => $brand
        ]);
    }

    /**
     * Store new brand
     */
    public function store(Request $request, CloudinaryService $cloudinary): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'logo' => 'nullable|image|mimes:jpeg,png,gif,webp|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            try {
                $validated['logo_url'] = $cloudinary->uploadImage($request->file('logo'), 'brands');
            } catch (\Exception $e) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không thể upload logo: ' . $e->getMessage()
                    ], 422);
                }
                return back()->withInput()->with('error', 'Không thể upload logo: ' . $e->getMessage());
            }
        }

        unset($validated['logo']);
        $brand = Brand::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm thương hiệu thành công!',
                'brand' => $brand
            ]);
        }

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
    public function update(Request $request, Brand $brand, CloudinaryService $cloudinary): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'logo' => 'nullable|image|mimes:jpeg,png,gif,webp|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            try {
                // Delete old logo if it exists
                if ($brand->logo_url && str_contains($brand->logo_url, 'cloudinary')) {
                    $cloudinary->deleteImage($brand->logo_url);
                }
                $validated['logo_url'] = $cloudinary->uploadImage($request->file('logo'), 'brands');
            } catch (\Exception $e) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không thể upload logo: ' . $e->getMessage()
                    ], 422);
                }
                return back()->withInput()->with('error', 'Không thể upload logo: ' . $e->getMessage());
            }
        }

        unset($validated['logo']);
        $brand->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật thương hiệu thành công!',
                'brand' => $brand->fresh()
            ]);
        }

        return redirect()->route('admin.brands.index')
            ->with('success', 'Đã cập nhật thương hiệu thành công!');
    }

    /**
     * Delete brand
     */
    public function destroy(Request $request, Brand $brand, CloudinaryService $cloudinary): RedirectResponse|JsonResponse
    {
        if ($brand->products()->count() > 0) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa thương hiệu này vì nó có sản phẩm liên kết.'
                ], 422);
            }
            return back()->with('error', 'Không thể xóa thương hiệu này vì nó có sản phẩm liên kết.');
        }

        // Delete logo from Cloudinary
        if ($brand->logo_url && str_contains($brand->logo_url, 'cloudinary')) {
            $cloudinary->deleteImage($brand->logo_url);
        }

        $brand->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa thương hiệu thành công!'
            ]);
        }

        return redirect()->route('admin.brands.index')
            ->with('success', 'Đã xóa thương hiệu thành công!');
    }
}

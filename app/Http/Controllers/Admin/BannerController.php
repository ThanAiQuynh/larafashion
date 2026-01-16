<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Services\CloudinaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BannerController extends Controller
{
    /**
     * Display banner list
     */
    public function index(): View
    {
        $banners = Banner::ordered()->paginate(10);
        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Show create form
     */
    public function create(): View
    {
        return view('admin.banners.create');
    }

    /**
     * Get banner data for editing (AJAX)
     */
    public function show(Banner $banner): JsonResponse
    {
        return response()->json([
            'success' => true,
            'banner' => $banner
        ]);
    }

    /**
     * Store new banner
     */
    public function store(Request $request, CloudinaryService $cloudinary): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image_file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'link_url' => 'nullable|url',
            'position' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề banner.',
            'image_file.required' => 'Vui lòng chọn ảnh banner.',
            'image_file.image' => 'File phải là ảnh.',
            'image_file.max' => 'Ảnh không được quá 5MB.',
        ]);

        // Upload image to Cloudinary
        $imageUrl = $cloudinary->uploadImage($request->file('image_file'), 'banners');

        $banner = Banner::create([
            'title' => $validated['title'],
            'image_url' => $imageUrl,
            'link_url' => $validated['link_url'],
            'position' => $validated['position'],
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm banner thành công!',
                'banner' => $banner
            ]);
        }

        return redirect()->route('admin.banners.index')
            ->with('success', 'Đã thêm banner thành công!');
    }

    /**
     * Show edit form
     */
    public function edit(Banner $banner): View
    {
        return view('admin.banners.edit', compact('banner'));
    }

    /**
     * Update banner
     */
    public function update(Request $request, Banner $banner, CloudinaryService $cloudinary): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'link_url' => 'nullable|url',
            'position' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data = [
            'title' => $validated['title'],
            'link_url' => $validated['link_url'],
            'position' => $validated['position'],
            'is_active' => $request->boolean('is_active'),
        ];

        // Upload new image if provided
        if ($request->hasFile('image_file')) {
            // Delete old image from Cloudinary
            if ($banner->image_url && str_contains($banner->image_url, 'cloudinary')) {
                $cloudinary->deleteImage($banner->image_url);
            }
            $data['image_url'] = $cloudinary->uploadImage($request->file('image_file'), 'banners');
        }

        $banner->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật banner thành công!',
                'banner' => $banner->fresh()
            ]);
        }

        return redirect()->route('admin.banners.index')
            ->with('success', 'Đã cập nhật banner thành công!');
    }

    /**
     * Delete banner
     */
    public function destroy(Request $request, Banner $banner, CloudinaryService $cloudinary): RedirectResponse|JsonResponse
    {
        // Delete image from Cloudinary
        if ($banner->image_url && str_contains($banner->image_url, 'cloudinary')) {
            $cloudinary->deleteImage($banner->image_url);
        }

        $banner->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa banner thành công!'
            ]);
        }

        return redirect()->route('admin.banners.index')
            ->with('success', 'Đã xóa banner thành công!');
    }

    /**
     * Toggle banner status
     */
    public function toggleStatus(Request $request, Banner $banner): RedirectResponse|JsonResponse
    {
        $banner->update(['is_active' => !$banner->is_active]);

        $status = $banner->is_active ? 'kích hoạt' : 'vô hiệu hóa';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Đã {$status} banner!",
                'is_active' => $banner->is_active
            ]);
        }

        return redirect()->back()
            ->with('success', "Đã {$status} banner!");
    }
}


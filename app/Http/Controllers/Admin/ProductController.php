<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display product list
     */
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'brand']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by brand
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $products = $query->paginate(15)->withQueryString();
        $categories = Category::whereNull('parent_id')->with('children')->get();
        $brands = Brand::all();

        return view('admin.products.index', compact('products', 'categories', 'brands'));
    }

    /**
     * Show create form
     */
    public function create(): View
    {
        $categories = Category::whereNull('parent_id')->with('children')->get();
        $brands = Brand::all();

        return view('admin.products.create', compact('categories', 'brands'));
    }

    /**
     * Store new product
     */
    public function store(Request $request, CloudinaryService $cloudinary): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:products,sku',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'thumbnail_url' => 'nullable|url',
            'thumbnail_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            // Variants validation
            'variants' => 'nullable|array',
            'variants.*.size' => 'nullable|string|max:20',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.color_code' => 'nullable|string|max:10',
            'variants.*.price_adjustment' => 'nullable|numeric',
            'variants.*.sku' => 'nullable|string|max:100',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');

        // Handle image upload to Cloudinary
        if ($request->hasFile('thumbnail_file')) {
            $validated['thumbnail_url'] = $cloudinary->uploadImage($request->file('thumbnail_file'), 'products');
        }

        // Extract variants before creating product
        $variants = $validated['variants'] ?? [];
        unset($validated['thumbnail_file'], $validated['variants'], $validated['stock_quantity']);
        $validated['stock_quantity'] = 0; // Force 0 on creation

        $product = Product::create($validated);

        // Create variants
        foreach ($variants as $variantData) {
            // Skip empty variants
            if (empty($variantData['size']) && empty($variantData['color'])) {
                continue;
            }

            $product->variants()->create([
                'size' => $variantData['size'] ?? null,
                'color' => $variantData['color'] ?? null,
                'color_code' => $variantData['color_code'] ?? null,
                'stock_quantity' => 0, // Force 0 on creation
                'price_adjustment' => $variantData['price_adjustment'] ?? 0,
                'sku' => $variantData['sku'] ?: $product->sku . '-' . Str::random(4),
                'is_active' => true,
            ]);
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Đã thêm sản phẩm thành công!');
    }

    /**
     * Show edit form
     */
    public function edit(Product $product): View
    {
        $categories = Category::whereNull('parent_id')->with('children')->get();
        $brands = Brand::all();
        $product->load('variants');

        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    /**
     * Update product
     */
    public function update(Request $request, Product $product, CloudinaryService $cloudinary): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:products,sku,' . $product->id,
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'thumbnail_url' => 'nullable|url',
            'thumbnail_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            // Variants validation
            'variants' => 'nullable|array',
            'variants.*.id' => 'nullable|integer',
            'variants.*.size' => 'nullable|string|max:20',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.color_code' => 'nullable|string|max:10',
            'variants.*.price_adjustment' => 'nullable|numeric',
            'variants.*.sku' => 'nullable|string|max:100',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');

        // Handle image upload to Cloudinary
        if ($request->hasFile('thumbnail_file')) {
            // Delete old image from Cloudinary
            if ($product->thumbnail_url && str_contains($product->thumbnail_url, 'cloudinary')) {
                $cloudinary->deleteImage($product->thumbnail_url);
            }

            $validated['thumbnail_url'] = $cloudinary->uploadImage($request->file('thumbnail_file'), 'products');
        }

        // Extract variants before updating product
        $variants = $validated['variants'] ?? [];
        unset($validated['thumbnail_file'], $validated['variants'], $validated['stock_quantity']);

        $product->update($validated);

        // Sync variants - collect IDs to keep
        $keepIds = [];
        foreach ($variants as $variantData) {
            // Skip empty variants
            if (empty($variantData['size']) && empty($variantData['color'])) {
                continue;
            }

            $variantFields = [
                'size' => $variantData['size'] ?? null,
                'color' => $variantData['color'] ?? null,
                'color_code' => $variantData['color_code'] ?? null,
                'price_adjustment' => $variantData['price_adjustment'] ?? 0,
                'sku' => $variantData['sku'] ?: $product->sku . '-' . Str::random(4),
                'is_active' => true,
            ];

            if (!empty($variantData['id'])) {
                // Update existing variant
                $variant = $product->variants()->find($variantData['id']);
                if ($variant) {
                    $variant->update($variantFields);
                    $keepIds[] = $variant->id;
                }
            } else {
                // Create new variant
                $newVariant = $product->variants()->create($variantFields);
                $keepIds[] = $newVariant->id;
            }
        }

        // Delete variants not in the keep list
        $product->variants()->whereNotIn('id', $keepIds)->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Đã cập nhật sản phẩm thành công!');
    }

    /**
     * Delete product
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Đã xóa sản phẩm thành công!');
    }

    /**
     * Toggle product status
     */
    public function toggleStatus(Product $product): RedirectResponse
    {
        $product->update(['is_active' => !$product->is_active]);

        $status = $product->is_active ? 'kích hoạt' : 'vô hiệu hóa';
        return redirect()->back()
            ->with('success', "Đã {$status} sản phẩm!");
    }
}

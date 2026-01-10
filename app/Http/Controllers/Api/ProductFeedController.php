<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductFeedController extends Controller
{
    /**
     * Get product feed for Tudongchat Knowledge Base
     * 
     * Returns a JSON list of active products with essential info
     * for training the chatbot.
     */
    public function index(): JsonResponse
    {
        $products = Product::query()
            ->active()
            ->inStock()
            ->select([
                'id',
                'name',
                'slug',
                'sku',
                'price',
                'original_price',
                'description',
                'thumbnail_url',
                'stock_quantity',
            ])
            ->with(['category:id,name,slug', 'brand:id,name,slug'])
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $feedData = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => number_format($product->price, 0, ',', '.') . ' VNĐ',
                'original_price' => $product->original_price 
                    ? number_format($product->original_price, 0, ',', '.') . ' VNĐ' 
                    : null,
                'description' => strip_tags($product->description),
                'category' => $product->category?->name,
                'brand' => $product->brand?->name,
                'image_url' => $product->thumbnail_url,
                'product_url' => url("/san-pham/{$product->slug}"),
                'in_stock' => $product->stock_quantity > 0,
            ];
        });

        return response()->json([
            'success' => true,
            'total' => $feedData->count(),
            'products' => $feedData,
            'updated_at' => now()->toIso8601String(),
        ]);
    }
}

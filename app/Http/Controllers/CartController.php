<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Display cart items
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('cart.index', compact('cart', 'total'));
    }

    /**
     * Add item to cart
     */
    public function add(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);
        $size = $request->input('size');
        $color = $request->input('color');

        $product = Product::with('variants')->findOrFail($productId);

        // Check if product has variants and validate selection
        $hasVariants = $product->variants->where('is_active', true)->count() > 0;
        $variant = null;
        $price = $product->price;

        if ($hasVariants) {
            // Find matching variant
            $variant = $product->variants()
                ->where('is_active', true)
                ->where(function ($q) use ($size) {
                    if ($size) {
                        $q->where('size', $size);
                    } else {
                        $q->whereNull('size');
                    }
                })
                ->where(function ($q) use ($color) {
                    if ($color) {
                        $q->where('color', $color);
                    } else {
                        $q->whereNull('color');
                    }
                })
                ->first();

            if (!$variant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Biến thể không tồn tại hoặc đã hết hàng.'
                ], 400);
            }

            // Check stock
            if ($variant->stock_quantity < $quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số lượng trong kho không đủ. Còn lại: ' . $variant->stock_quantity
                ], 400);
            }

            // Calculate price with adjustment
            $price = $product->price + ($variant->price_adjustment ?? 0);
        }

        $cart = session()->get('cart', []);

        // Create cart key (product_id or product_id-size-color for variants)
        $cartKey = $productId;
        if ($size || $color) {
            $cartKey = $productId . '-' . ($size ?? 'none') . '-' . ($color ?? 'none');
        }

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            $cart[$cartKey] = [
                "id" => $product->id,
                "variant_id" => $variant?->id,
                "name" => $product->name,
                "size" => $size,
                "color" => $color,
                "quantity" => $quantity,
                "price" => $price,
                "thumbnail_url" => $product->thumbnail_url,
                "slug" => $product->slug
            ];
        }

        session()->put('cart', $cart);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm sản phẩm vào giỏ hàng!',
                'cart_count' => count($cart)
            ]);
        }

        return redirect()->back()->with('success', 'Đã thêm sản phẩm vào giỏ hàng!');
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request)
    {
        if ($request->id && $request->quantity) {
            $cart = session()->get('cart');
            if (isset($cart[$request->id])) {
                $cart[$request->id]["quantity"] = $request->quantity;
                session()->put('cart', $cart);

                $itemTotal = $cart[$request->id]['price'] * $cart[$request->id]['quantity'];
                $cartTotal = 0;
                foreach ($cart as $item) {
                    $cartTotal += $item['price'] * $item['quantity'];
                }

                return response()->json([
                    'success' => true,
                    'item_total' => number_format($itemTotal, 0, ',', '.') . 'đ',
                    'cart_total' => number_format($cartTotal, 0, ',', '.') . 'đ'
                ]);
            }
        }
        return response()->json(['success' => false], 400);
    }

    /**
     * Remove item from cart
     */
    public function remove(Request $request)
    {
        if ($request->id) {
            $cart = session()->get('cart');
            if (isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);

                $cartTotal = 0;
                foreach ($cart as $item) {
                    $cartTotal += $item['price'] * $item['quantity'];
                }

                return response()->json([
                    'success' => true,
                    'cart_total' => number_format($cartTotal, 0, ',', '.') . 'đ',
                    'cart_count' => count($cart)
                ]);
            }
        }
        return response()->json(['success' => false], 400);
    }

    /**
     * Clear cart
     */
    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('cart.index')->with('success', 'Đã xóa toàn bộ giỏ hàng!');
    }
}

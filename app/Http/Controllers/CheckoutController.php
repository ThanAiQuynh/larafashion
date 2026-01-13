<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\VNPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Display checkout form
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Get user addresses
        $addresses = [];
        $defaultAddress = null;
        if (auth()->check()) {
            $addresses = auth()->user()->addresses()->orderBy('is_default', 'desc')->get();
            $defaultAddress = $addresses->where('is_default', true)->first();
        }

        return view('checkout.index', compact('cart', 'total', 'addresses', 'defaultAddress'));
    }

    /**
     * Process checkout
     */
    public function process(Request $request, VNPayService $vnpay)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'payment_method' => 'required|in:cod,vnpay',
        ], [
            'customer_name.required' => 'Vui lòng nhập họ tên.',
            'customer_email.required' => 'Vui lòng nhập email.',
            'customer_email.email' => 'Email không hợp lệ.',
            'customer_phone.required' => 'Vui lòng nhập số điện thoại.',
            'shipping_address.required' => 'Vui lòng nhập địa chỉ giao hàng.',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        try {
            DB::beginTransaction();

            $total = 0;
            foreach ($cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            // Create Order
            $order = Order::create([
                'order_code' => Order::generateOrderCode(),
                'user_id' => auth()->id(),
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'total_amount' => $total,
                'shipping_address' => [
                    'address' => $request->shipping_address,
                ],
                'payment_method' => $request->payment_method === 'vnpay' ? 'vnpay' : 'cod',
                'status' => 'pending',
                'payment_status' => 'pending',
            ]);

            // Create OrderItems and Deduct Stock
            foreach ($cart as $id => $item) {
                $product = Product::findOrFail($id);
                
                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Sản phẩm {$product->name} không đủ hàng.");
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['price'] * $item['quantity'],
                ]);

                $product->decrement('stock_quantity', $item['quantity']);
            }

            DB::commit();

            // Clear Cart
            session()->forget('cart');

            // If VNPay, redirect to payment
            if ($request->payment_method === 'vnpay') {
                $paymentUrl = $vnpay->createPaymentUrl($order, $request->ip());
                return redirect()->away($paymentUrl);
            }

            return redirect()->route('checkout.success', $order->id)->with('success', 'Đặt hàng thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Handle VNPay return
     */
    public function vnpayReturn(Request $request, VNPayService $vnpay)
    {
        // Validate signature
        if (!$vnpay->validateReturn($request)) {
            return redirect()->route('home')->with('error', 'Xác thực thanh toán thất bại.');
        }

        $orderCode = $vnpay->getTransactionRef($request);
        $order = Order::where('order_code', $orderCode)->first();

        if (!$order) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng.');
        }

        if ($vnpay->isPaymentSuccess($request)) {
            // Payment successful
            $order->update([
                'payment_status' => 'paid',
                'vnpay_transaction_id' => $request->input('vnp_TransactionNo'),
            ]);
            return redirect()->route('checkout.success', $order->id)
                ->with('success', 'Thanh toán thành công!');
        } else {
            // Payment failed
            $responseCode = $request->input('vnp_ResponseCode');
            $message = $vnpay->getResponseMessage($responseCode);
            
            $order->update([
                'payment_status' => 'failed',
                'notes' => 'VNPay Error: ' . $message,
            ]);
            
            return redirect()->route('checkout.success', $order->id)
                ->with('error', 'Thanh toán thất bại: ' . $message);
        }
    }

    /**
     * Display success page
     */
    public function success($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        return view('checkout.success', compact('order'));
    }
}

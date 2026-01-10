<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    /**
     * Show account dashboard
     */
    public function index()
    {
        $recentOrders = Auth::user()->orders()->latest()->take(5)->get();
        return view('account.index', compact('recentOrders'));
    }

    /**
     * Show order history
     */
    public function orders()
    {
        $orders = Auth::user()->orders()->with('items')->latest()->paginate(10);
        return view('account.orders', compact('orders'));
    }

    /**
     * Show order detail
     */
    public function orderDetail(Order $order)
    {
        // Ensure user owns this order
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('items.product');
        return view('account.order-detail', compact('order'));
    }

    /**
     * Show profile edit form
     */
    public function profile()
    {
        return view('account.profile');
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
        ]);

        Auth::user()->update([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
        ]);

        return back()->with('success', 'Cập nhật thông tin thành công!');
    }

    /**
     * Show password change form
     */
    public function password()
    {
        return view('account.password');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->with('error', 'Mật khẩu hiện tại không đúng.');
        }

        Auth::user()->update([
            'password' => $request->password,
        ]);

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }
}

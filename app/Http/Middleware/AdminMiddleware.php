<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     * Check if user is authenticated and has admin role.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $user = Auth::user();

        if ($user->role !== 'admin') {
            Auth::logout();
            return redirect()->route('admin.login')
                ->with('error', 'Bạn không có quyền truy cập.');
        }

        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('admin.login')
                ->with('error', 'Tài khoản đã bị vô hiệu hóa.');
        }

        return $next($request);
    }
}

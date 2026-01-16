<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display user list
     */
    public function index(Request $request): View
    {
        $query = User::query()->withCount('orders');

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // Sorting
        $query->orderBy('created_at', 'desc');

        $users = $query->paginate(15)->withQueryString();

        // Stats
        $stats = [
            'total' => User::count(),
            'admins' => User::admins()->count(),
            'customers' => User::customers()->count(),
            'active' => User::active()->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Show user details
     */
    public function show(User $user): View
    {
        $user->load([
            'orders' => function ($query) {
                $query->orderBy('created_at', 'desc')->take(10);
            },
            'reviews' => function ($query) {
                $query->with('product')->orderBy('created_at', 'desc')->take(5);
            }
        ]);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show create form
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Store new user
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'nullable|string|max:20',
            'role' => 'required|in:admin,customer',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.unique' => 'Email đã tồn tại.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'] ?? null,
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Đã thêm người dùng thành công!');
    }

    /**
     * Show edit form
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'phone_number' => 'nullable|string|max:20',
            'role' => 'required|in:admin,customer',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'] ?? null,
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active'),
        ];

        // Only update password if provided
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Đã cập nhật người dùng thành công!');
    }

    /**
     * Delete user
     */
    public function destroy(User $user): RedirectResponse
    {
        // Prevent self-delete
        if ($user->id === \Illuminate\Support\Facades\Auth::guard('admin')->id()) {
            return redirect()->back()
                ->with('error', 'Không thể xóa tài khoản của chính mình!');
        }

        // Check if user has orders
        if ($user->orders()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Không thể xóa người dùng có đơn hàng!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Đã xóa người dùng thành công!');
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        // Prevent deactivating self
        if ($user->id === \Illuminate\Support\Facades\Auth::guard('admin')->id()) {
            return redirect()->back()
                ->with('error', 'Không thể vô hiệu hóa tài khoản của chính mình!');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'kích hoạt' : 'vô hiệu hóa';
        return redirect()->back()
            ->with('success', "Đã {$status} người dùng!");
    }
}

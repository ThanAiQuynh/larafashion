<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProductStoreController;
use App\Http\Controllers\ReviewController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', function () {
    $featuredProducts = Product::query()
        ->active()
        ->featured()
        ->with(['brand'])
        ->take(10)
        ->get();

    $products = Product::query()
        ->active()
        ->inStock()
        ->with(['brand'])
        ->orderBy('created_at', 'desc')
        ->take(12)
        ->get();

    $saleProducts = Product::query()
        ->active()
        ->whereNotNull('original_price')
        ->where('original_price', '>', 0)
        ->with(['brand'])
        ->orderBy('created_at', 'desc')
        ->take(8)
        ->get();

    $categories = \App\Models\Category::active()
        ->parents()
        ->orderBy('name')
        ->get();

    $totalProductCount = Product::active()->inStock()->count();

    return view('home', compact('featuredProducts', 'products', 'saleProducts', 'categories', 'totalProductCount'));
})->name('home');

// Products
Route::get('/san-pham', [ProductStoreController::class, 'index'])->name('products.index');
Route::get('/san-pham/{slug}', [ProductStoreController::class, 'show'])->name('products.show');

// Sale Products Page
Route::get('/khuyen-mai', function () {
    $products = \App\Models\Product::active()
        ->whereNotNull('original_price')
        ->where('original_price', '>', 0)
        ->with(['brand', 'category'])
        ->orderBy('created_at', 'desc')
        ->paginate(12);

    $categories = \App\Models\Category::whereNull('parent_id')->with('children')->get();
    $brands = \App\Models\Brand::all();
    $maxPrice = 3000000;

    return view('products.index', [
        'products' => $products,
        'categories' => $categories,
        'brands' => $brands,
        'maxPrice' => $maxPrice,
        'pageTitle' => 'Sản phẩm khuyến mãi',
        'isSalePage' => true,
    ]);
})->name('products.sale');

// Product Reviews
Route::post('/san-pham/{product}/danh-gia', [ReviewController::class, 'store'])->name('reviews.store')->middleware('auth');
Route::delete('/danh-gia/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy')->middleware('auth');

// Cart
Route::prefix('gio-hang')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/update', [CartController::class, 'update'])->name('update');
    Route::post('/remove', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
});

// Checkout
Route::prefix('thanh-toan')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/process', [CheckoutController::class, 'process'])->name('process');
    Route::get('/success/{id}', [CheckoutController::class, 'success'])->name('success');
    Route::get('/vnpay-return', [CheckoutController::class, 'vnpayReturn'])->name('vnpay.return');
    Route::post('/check-voucher', [App\Http\Controllers\VoucherController::class, 'check'])->name('check-voucher');
});

// Customer Authentication
Route::middleware('guest:web')->group(function () {
    Route::get('/dang-nhap', [App\Http\Controllers\Auth\CustomerAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/dang-nhap', [App\Http\Controllers\Auth\CustomerAuthController::class, 'login']);
    Route::get('/dang-ky', [App\Http\Controllers\Auth\CustomerAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/dang-ky', [App\Http\Controllers\Auth\CustomerAuthController::class, 'register']);

    // Forgot Password
    Route::get('/quen-mat-khau', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
    Route::post('/quen-mat-khau', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
    Route::get('/dat-lai-mat-khau/{token}', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/dat-lai-mat-khau', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/dang-xuat', [App\Http\Controllers\Auth\CustomerAuthController::class, 'logout'])->name('logout');

    // Customer Account
    Route::prefix('tai-khoan')->name('account.')->group(function () {
        Route::get('/', [App\Http\Controllers\AccountController::class, 'index'])->name('index');
        Route::get('/don-hang', [App\Http\Controllers\AccountController::class, 'orders'])->name('orders');
        Route::get('/don-hang/{order}', [App\Http\Controllers\AccountController::class, 'orderDetail'])->name('orders.detail');
        Route::get('/thong-tin', [App\Http\Controllers\AccountController::class, 'profile'])->name('profile');
        Route::put('/thong-tin', [App\Http\Controllers\AccountController::class, 'updateProfile'])->name('profile.update');
        Route::get('/mat-khau', [App\Http\Controllers\AccountController::class, 'password'])->name('password');
        Route::put('/mat-khau', [App\Http\Controllers\AccountController::class, 'updatePassword'])->name('password.update');

        // Address Management
        Route::resource('dia-chi', App\Http\Controllers\UserAddressController::class)
            ->parameters(['dia-chi' => 'address'])
            ->names('addresses');
        Route::post('/dia-chi/{address}/mac-dinh', [App\Http\Controllers\UserAddressController::class, 'setDefault'])->name('addresses.default');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    // Auth Routes (Guest only)
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    });

    // Protected Admin Routes
    Route::middleware('admin')->group(function () {
        // Logout
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Products Management
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/create', [ProductController::class, 'create'])->name('create');
            Route::post('/', [ProductController::class, 'store'])->name('store');
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('/{product}', [ProductController::class, 'update'])->name('update');
            Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
            Route::patch('/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Categories Management
        Route::resource('categories', CategoryController::class);

        // Brands Management
        Route::resource('brands', BrandController::class);

        // Banners Management
        Route::prefix('banners')->name('banners.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\BannerController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\BannerController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\BannerController::class, 'store'])->name('store');
            Route::get('/{banner}', [\App\Http\Controllers\Admin\BannerController::class, 'show'])->name('show');
            Route::get('/{banner}/edit', [\App\Http\Controllers\Admin\BannerController::class, 'edit'])->name('edit');
            Route::put('/{banner}', [\App\Http\Controllers\Admin\BannerController::class, 'update'])->name('update');
            Route::delete('/{banner}', [\App\Http\Controllers\Admin\BannerController::class, 'destroy'])->name('destroy');
            Route::patch('/{banner}/toggle-status', [\App\Http\Controllers\Admin\BannerController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Orders Management
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/{order}', [OrderController::class, 'show'])->name('show');
            Route::patch('/{order}/status', [OrderController::class, 'updateStatus'])->name('update-status');
            Route::patch('/{order}/payment', [OrderController::class, 'updatePaymentStatus'])->name('update-payment');
        });

        // Users Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('store');
            Route::get('/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('destroy');
            Route::patch('/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Suppliers Management
        Route::resource('suppliers', \App\Http\Controllers\Admin\SupplierController::class)->except(['create', 'edit']);

        // Stock Imports Management
        Route::prefix('stock-imports')->name('stock-imports.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\StockImportController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\StockImportController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\StockImportController::class, 'store'])->name('store');
            Route::get('/{stockImport}', [\App\Http\Controllers\Admin\StockImportController::class, 'show'])->name('show');
            Route::post('/{stockImport}/confirm', [\App\Http\Controllers\Admin\StockImportController::class, 'confirm'])->name('confirm');
            Route::post('/{stockImport}/cancel', [\App\Http\Controllers\Admin\StockImportController::class, 'cancel'])->name('cancel');
            Route::delete('/{stockImport}', [\App\Http\Controllers\Admin\StockImportController::class, 'destroy'])->name('destroy');
        });

        // Vouchers Management
        Route::resource('vouchers', \App\Http\Controllers\Admin\VoucherController::class)->except(['create', 'edit']);

        // AI Assistant
        Route::prefix('ai-assistant')->name('ai-assistant.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AdminAIController::class, 'index'])->name('index');
            Route::post('/ask', [\App\Http\Controllers\Admin\AdminAIController::class, 'ask'])->name('ask');
        });
    });
});


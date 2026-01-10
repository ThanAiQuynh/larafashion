<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\ProductReview;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function index(): View
    {
        // Basic stats
        $stats = [
            'products' => Product::count(),
            'orders' => Order::count(),
            'customers' => User::where('role', 'customer')->count(),
            'revenue' => Order::where('status', 'completed')->sum('total_amount'),
            'orders_today' => Order::whereDate('created_at', today())->count(),
            'revenue_today' => Order::where('status', 'completed')->whereDate('created_at', today())->sum('total_amount'),
            'reviews' => ProductReview::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
        ];

        // Revenue by month (last 6 months)
        $revenueByMonth = Order::where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(total_amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => Carbon::createFromDate($item->year, $item->month, 1)->format('m/Y'),
                    'value' => $item->total,
                ];
            });

        // Orders by status
        $ordersByStatus = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Top 5 selling products
        $topProducts = Product::select('products.*')
            ->selectRaw('SUM(order_items.quantity) as total_sold')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->groupBy('products.id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Recent orders
        $recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Low stock products (< 10)
        $lowStockProducts = Product::where('stock_quantity', '<', 10)
            ->where('stock_quantity', '>', 0)
            ->orderBy('stock_quantity')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'revenueByMonth',
            'ordersByStatus',
            'topProducts',
            'recentOrders',
            'lowStockProducts'
        ));
    }
}

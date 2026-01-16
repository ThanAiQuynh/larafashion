<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AdminAIService
{
    /**
     * Process a question and return an answer
     */
    public function ask(string $question): array
    {
        $question = Str::lower($question);

        // Detect intent and time frame
        $intent = $this->detectIntent($question);
        $timeFrame = $this->detectTimeFrame($question);

        // Process based on intent
        return match ($intent) {
            'revenue' => $this->getRevenueData($timeFrame),
            'orders' => $this->getOrdersData($timeFrame),
            'products_bestseller' => $this->getBestSellerProducts($timeFrame),
            'products_lowstock' => $this->getLowStockProducts(),
            'customers' => $this->getCustomersData($timeFrame),
            'orders_pending' => $this->getPendingOrders(),
            'vouchers' => $this->getVouchersData(),
            'brands' => $this->getBrandsData(),
            'categories' => $this->getCategoriesData(),
            'suppliers' => $this->getSuppliersData(),
            default => $this->getDefaultResponse($question),
        };
    }

    /**
     * Detect the intent/topic of the question
     */
    private function detectIntent(string $question): string
    {
        // Revenue related
        if (Str::contains($question, ['doanh thu', 'revenue', 'tiền', 'thu nhập', 'bán được'])) {
            return 'revenue';
        }

        // Best seller products
        if (Str::contains($question, ['bán chạy', 'best seller', 'hot', 'phổ biến', 'nhiều nhất'])) {
            return 'products_bestseller';
        }

        // Low stock products
        if (Str::contains($question, ['tồn kho', 'hết hàng', 'sắp hết', 'stock', 'còn ít'])) {
            return 'products_lowstock';
        }

        // Orders related
        if (Str::contains($question, ['đơn hàng', 'order', 'đơn'])) {
            if (Str::contains($question, ['chờ', 'pending', 'chưa xử lý', 'mới'])) {
                return 'orders_pending';
            }
            return 'orders';
        }

        // Customers related
        if (Str::contains($question, ['khách hàng', 'customer', 'user', 'người dùng', 'thành viên'])) {
            return 'customers';
        }

        // Vouchers related
        if (Str::contains($question, ['voucher', 'mã giảm giá', 'khuyến mãi', 'giảm giá'])) {
            return 'vouchers';
        }

        // Brands related
        if (Str::contains($question, ['thương hiệu', 'brand', 'nhãn hàng'])) {
            return 'brands';
        }

        // Categories related
        if (Str::contains($question, ['danh mục', 'category', 'loại sản phẩm'])) {
            return 'categories';
        }

        // Suppliers related
        if (Str::contains($question, ['nhà cung cấp', 'supplier', 'nguồn hàng'])) {
            return 'suppliers';
        }

        return 'unknown';
    }

    /**
     * Detect time frame from question
     */
    private function detectTimeFrame(string $question): array
    {
        $now = Carbon::now();

        if (Str::contains($question, ['hôm nay', 'today', 'ngày hôm nay'])) {
            return [
                'label' => 'hôm nay',
                'start' => $now->copy()->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ];
        }

        if (Str::contains($question, ['hôm qua', 'yesterday'])) {
            return [
                'label' => 'hôm qua',
                'start' => $now->copy()->subDay()->startOfDay(),
                'end' => $now->copy()->subDay()->endOfDay(),
            ];
        }

        if (Str::contains($question, ['tuần này', 'this week', 'tuần'])) {
            return [
                'label' => 'tuần này',
                'start' => $now->copy()->startOfWeek(),
                'end' => $now->copy()->endOfWeek(),
            ];
        }

        if (Str::contains($question, ['tháng này', 'this month', 'tháng'])) {
            return [
                'label' => 'tháng này',
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfMonth(),
            ];
        }

        if (Str::contains($question, ['năm nay', 'this year', 'năm'])) {
            return [
                'label' => 'năm nay',
                'start' => $now->copy()->startOfYear(),
                'end' => $now->copy()->endOfYear(),
            ];
        }

        // Default to today
        return [
            'label' => 'hôm nay',
            'start' => $now->copy()->startOfDay(),
            'end' => $now->copy()->endOfDay(),
        ];
    }

    /**
     * Get revenue data
     */
    private function getRevenueData(array $timeFrame): array
    {
        $revenue = Order::whereBetween('created_at', [$timeFrame['start'], $timeFrame['end']])
            ->whereIn('status', ['completed', 'shipping', 'confirmed'])
            ->sum('total_amount');

        $orderCount = Order::whereBetween('created_at', [$timeFrame['start'], $timeFrame['end']])
            ->whereIn('status', ['completed', 'shipping', 'confirmed'])
            ->count();

        return [
            'success' => true,
            'message' => "📊 **Doanh thu {$timeFrame['label']}:**\n\n" .
                "💰 Tổng doanh thu: **" . number_format($revenue, 0, ',', '.') . "đ**\n" .
                "📦 Số đơn hàng: **{$orderCount}** đơn\n" .
                ($orderCount > 0 ? "📈 Trung bình/đơn: **" . number_format($revenue / $orderCount, 0, ',', '.') . "đ**" : ""),
            'type' => 'revenue',
        ];
    }

    /**
     * Get orders data
     */
    private function getOrdersData(array $timeFrame): array
    {
        $orders = Order::whereBetween('created_at', [$timeFrame['start'], $timeFrame['end']])->get();

        $total = $orders->count();
        $pending = $orders->where('status', 'pending')->count();
        $confirmed = $orders->where('status', 'confirmed')->count();
        $shipping = $orders->where('status', 'shipping')->count();
        $completed = $orders->where('status', 'completed')->count();
        $cancelled = $orders->where('status', 'cancelled')->count();

        return [
            'success' => true,
            'message' => "📦 **Thống kê đơn hàng {$timeFrame['label']}:**\n\n" .
                "📋 Tổng đơn hàng: **{$total}** đơn\n" .
                "⏳ Chờ xử lý: **{$pending}** đơn\n" .
                "✅ Đã xác nhận: **{$confirmed}** đơn\n" .
                "🚚 Đang giao: **{$shipping}** đơn\n" .
                "✓ Hoàn thành: **{$completed}** đơn\n" .
                "❌ Đã hủy: **{$cancelled}** đơn",
            'type' => 'orders',
        ];
    }

    /**
     * Get pending orders
     */
    private function getPendingOrders(): array
    {
        $pending = Order::where('status', 'pending')->count();
        $pendingToday = Order::where('status', 'pending')
            ->whereDate('created_at', Carbon::today())
            ->count();

        return [
            'success' => true,
            'message' => "⏳ **Đơn hàng chờ xử lý:**\n\n" .
                "📋 Tổng đơn chờ: **{$pending}** đơn\n" .
                "🆕 Đơn mới hôm nay: **{$pendingToday}** đơn\n\n" .
                ($pending > 0 ? "👉 [Xem danh sách đơn hàng](/admin/orders?status=pending)" : "✅ Không có đơn nào cần xử lý!"),
            'type' => 'orders_pending',
        ];
    }

    /**
     * Get best seller products
     */
    private function getBestSellerProducts(array $timeFrame): array
    {
        $products = \DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->whereBetween('orders.created_at', [$timeFrame['start'], $timeFrame['end']])
            ->whereIn('orders.status', ['completed', 'shipping', 'confirmed'])
            ->select('products.name', \DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        if ($products->isEmpty()) {
            return [
                'success' => true,
                'message' => "📊 **Sản phẩm bán chạy {$timeFrame['label']}:**\n\n" .
                    "Chưa có dữ liệu bán hàng trong khoảng thời gian này.",
                'type' => 'products',
            ];
        }

        $list = "";
        foreach ($products as $index => $product) {
            $rank = $index + 1;
            $list .= "{$rank}. {$product->name} - **{$product->total_qty}** sản phẩm\n";
        }

        return [
            'success' => true,
            'message' => "🏆 **Top 5 sản phẩm bán chạy {$timeFrame['label']}:**\n\n{$list}",
            'type' => 'products_bestseller',
        ];
    }

    /**
     * Get low stock products
     */
    private function getLowStockProducts(): array
    {
        $lowStock = Product::where('stock_quantity', '<', 10)
            ->where('stock_quantity', '>', 0)
            ->where('is_active', true)
            ->orderBy('stock_quantity')
            ->limit(10)
            ->get();

        $outOfStock = Product::where('stock_quantity', '<=', 0)
            ->where('is_active', true)
            ->count();

        if ($lowStock->isEmpty() && $outOfStock == 0) {
            return [
                'success' => true,
                'message' => "✅ **Tình trạng tồn kho:**\n\n" .
                    "Tất cả sản phẩm đều có đủ hàng!",
                'type' => 'products_lowstock',
            ];
        }

        $list = "";
        foreach ($lowStock as $product) {
            $list .= "⚠️ {$product->name}: còn **{$product->stock_quantity}** sản phẩm\n";
        }

        return [
            'success' => true,
            'message' => "📦 **Cảnh báo tồn kho:**\n\n" .
                "❌ Hết hàng: **{$outOfStock}** sản phẩm\n" .
                "⚠️ Sắp hết (< 10): **{$lowStock->count()}** sản phẩm\n\n" .
                ($list ? "**Chi tiết sắp hết hàng:**\n{$list}" : "") .
                "\n👉 [Xem quản lý sản phẩm](/admin/products)",
            'type' => 'products_lowstock',
        ];
    }

    /**
     * Get customers data
     */
    private function getCustomersData(array $timeFrame): array
    {
        $newCustomers = User::where('role', 'customer')
            ->whereBetween('created_at', [$timeFrame['start'], $timeFrame['end']])
            ->count();

        $totalCustomers = User::where('role', 'customer')->count();
        $activeCustomers = User::where('role', 'customer')->where('is_active', true)->count();

        return [
            'success' => true,
            'message' => "👥 **Thống kê khách hàng {$timeFrame['label']}:**\n\n" .
                "🆕 Khách hàng mới: **{$newCustomers}** người\n" .
                "📊 Tổng khách hàng: **{$totalCustomers}** người\n" .
                "✅ Đang hoạt động: **{$activeCustomers}** người",
            'type' => 'customers',
        ];
    }

    /**
     * Get vouchers data
     */
    private function getVouchersData(): array
    {
        $vouchers = \App\Models\Voucher::active()->get();
        $upcoming = \App\Models\Voucher::where('is_active', true)
            ->where('start_date', '>', now())
            ->count();
        $expired = \App\Models\Voucher::where('end_date', '<', now())
            ->count();

        if ($vouchers->isEmpty()) {
            return [
                'success' => true,
                'message' => "🎫 **Thông tin Voucher:**\n\nKhông có voucher nào đang được áp dụng tại cửa hàng.\n\n" .
                    ($upcoming > 0 ? "⏳ Sắp diễn ra: **{$upcoming}** voucher mới\n" : "") .
                    "👉 [Quản lý Voucher](/admin/vouchers)",
                'type' => 'vouchers',
            ];
        }

        $list = "";
        foreach ($vouchers as $voucher) {
            $discount = $voucher->type === 'fixed'
                ? number_format((float) $voucher->value, 0, ',', '.') . "đ"
                : $voucher->value . "%";
            $list .= "• **{$voucher->code}**: Giảm {$discount} (Còn lại: {$voucher->quantity})\n";
        }

        return [
            'success' => true,
            'message' => "🎫 **Các Voucher đang hoạt động:**\n\n{$list}\n" .
                ($upcoming > 0 ? "⏳ Sắp diễn ra: **{$upcoming}** voucher mới\n" : "") .
                "👉 [Quản lý Voucher](/admin/vouchers)",
            'type' => 'vouchers',
        ];
    }

    /**
     * Get brands data
     */
    private function getBrandsData(): array
    {
        $brands = \App\Models\Brand::withCount('products')->get();

        if ($brands->isEmpty()) {
            return [
                'success' => true,
                'message' => "🏷️ **Thông tin Thương hiệu:**\n\nChưa có thương hiệu nào được tạo.",
                'type' => 'brands',
            ];
        }

        $list = "";
        foreach ($brands as $brand) {
            $list .= "• **{$brand->name}**: {$brand->products_count} sản phẩm\n";
        }

        return [
            'success' => true,
            'message' => "🏷️ **Danh sách thương hiệu:**\n\n{$list}\n" .
                "👉 [Quản lý thương hiệu](/admin/brands)",
            'type' => 'brands',
        ];
    }

    /**
     * Get categories data
     */
    private function getCategoriesData(): array
    {
        $categories = \App\Models\Category::whereNull('parent_id')->withCount('children')->get();

        if ($categories->isEmpty()) {
            return [
                'success' => true,
                'message' => "📁 **Thông tin Danh mục:**\n\nChưa có danh mục nào được tạo.",
                'type' => 'categories',
            ];
        }

        $list = "";
        foreach ($categories as $category) {
            $list .= "• **{$category->name}**: {$category->children_count} danh mục con\n";
        }

        return [
            'success' => true,
            'message' => "📁 **Danh mục chính:**\n\n{$list}\n" .
                "👉 [Quản lý danh mục](/admin/categories)",
            'type' => 'categories',
        ];
    }

    /**
     * Get suppliers data
     */
    private function getSuppliersData(): array
    {
        $count = \App\Models\Supplier::count();
        $recent = \App\Models\StockImport::where('status', 'pending')->count();

        return [
            'success' => true,
            'message' => "🤝 **Thông tin Nhà cung cấp:**\n\n" .
                "🏢 Tổng số nhà cung cấp: **{$count}**\n" .
                "📦 Đơn nhập hàng chờ xử lý: **{$recent}** đơn\n\n" .
                "👉 [Quản lý nhà cung cấp](/admin/suppliers)",
            'type' => 'suppliers',
        ];
    }

    /**
     * Default response for unknown questions
     */
    private function getDefaultResponse(string $question): array
    {
        return [
            'success' => true,
            'message' => "🤔 Xin lỗi, tôi chưa hiểu câu hỏi của bạn.\n\n" .
                "**Tôi có thể trả lời các câu hỏi về:**\n" .
                "💰 **Doanh thu** (hôm nay, tuần này, tháng này)\n" .
                "📦 **Đơn hàng** (tổng, đang chờ xử lý)\n" .
                "🏆 **Sản phẩm** (bán chạy, tồn kho)\n" .
                "🎫 **Voucher** (mã giảm giá đang hoạt động)\n" .
                "🏷️ **Thương hiệu** & **Danh mục**\n" .
                "👥 **Khách hàng** & **Nhà cung cấp**\n\n" .
                "**Ví dụ câu hỏi:**\n" .
                "• Cho tôi biết về các voucher giảm giá?\n" .
                "• Các thương hiệu có trong cửa hàng?\n" .
                "• Doanh thu hôm nay là bao nhiêu?",
            'type' => 'help',
        ];
    }
}

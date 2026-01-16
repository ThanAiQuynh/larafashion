<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('size', 20)->nullable(); // S, M, L, XL, XXL, etc.
            $table->string('color', 50)->nullable(); // Tên màu: Đen, Trắng, Đỏ...
            $table->string('color_code', 10)->nullable(); // Hex color: #ffffff
            $table->integer('stock_quantity')->default(0);
            $table->decimal('price_adjustment', 12, 2)->default(0); // Điều chỉnh giá (+/-)
            $table->string('sku', 100)->nullable()->unique(); // SKU riêng cho variant
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Đảm bảo mỗi product chỉ có 1 variant với cùng size và color
            $table->unique(['product_id', 'size', 'color'], 'product_size_color_unique');
        });

        // Thêm cột size và color vào order_items để lưu thông tin khi đặt hàng
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('size', 20)->nullable()->after('product_name');
            $table->string('color', 50)->nullable()->after('size');
            $table->foreignId('variant_id')->nullable()->after('product_id')->constrained('product_variants')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['variant_id']);
            $table->dropColumn(['size', 'color', 'variant_id']);
        });

        Schema::dropIfExists('product_variants');
    }
};

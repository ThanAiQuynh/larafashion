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
        // Bảng voucher
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->enum('type', ['percentage', 'fixed_amount'])->default('percentage');
            $table->decimal('value', 12, 2); // % hoặc số tiền
            $table->decimal('min_order_value', 12, 2)->default(0); // Đơn tối thiểu
            $table->decimal('max_discount', 12, 2)->nullable(); // Giảm tối đa (cho %)
            $table->integer('usage_limit')->nullable(); // Giới hạn tổng lượt
            $table->integer('usage_count')->default(0); // Đã sử dụng
            $table->integer('usage_per_user')->default(1); // Giới hạn mỗi user
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Lịch sử sử dụng voucher
        Schema::create('voucher_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->decimal('discount_amount', 12, 2);
            $table->timestamps();
        });

        // Thêm cột voucher vào bảng orders
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('voucher_id')->nullable()->after('payment_method')->constrained()->nullOnDelete();
            $table->decimal('discount_amount', 12, 2)->default(0)->after('voucher_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['voucher_id']);
            $table->dropColumn(['voucher_id', 'discount_amount']);
        });

        Schema::dropIfExists('voucher_usages');
        Schema::dropIfExists('vouchers');
    }
};

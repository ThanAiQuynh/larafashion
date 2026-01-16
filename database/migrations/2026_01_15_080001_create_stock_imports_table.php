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
        // Phiếu nhập hàng
        Schema::create('stock_imports', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique(); // PN-YYYYMMDD-XXX
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->date('import_date');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Chi tiết phiếu nhập
        Schema::create('stock_import_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_import_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('total_price', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_import_items');
        Schema::dropIfExists('stock_imports');
    }
};

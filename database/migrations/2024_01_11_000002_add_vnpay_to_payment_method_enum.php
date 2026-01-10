<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify payment_method to include vnpay
        DB::statement("ALTER TABLE `orders` MODIFY `payment_method` ENUM('cod', 'banking', 'vnpay') DEFAULT 'cod'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `orders` MODIFY `payment_method` ENUM('cod', 'banking') DEFAULT 'cod'");
    }
};

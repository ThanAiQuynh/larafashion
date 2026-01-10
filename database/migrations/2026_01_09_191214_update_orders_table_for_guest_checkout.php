<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $blueprint) {
            $blueprint->bigInteger('user_id')->unsigned()->nullable()->change();
            $blueprint->string('customer_name')->after('order_code')->nullable();
            $blueprint->string('customer_email')->after('customer_name')->nullable();
            $blueprint->string('customer_phone')->after('customer_email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $blueprint) {
            $blueprint->bigInteger('user_id')->unsigned()->nullable(false)->change();
            $blueprint->dropColumn(['customer_name', 'customer_email', 'customer_phone']);
        });
    }
};

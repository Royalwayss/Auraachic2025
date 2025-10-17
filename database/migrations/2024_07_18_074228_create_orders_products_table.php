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
        Schema::create('orders_products', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->integer('user_id');
            $table->integer('product_id');
            $table->string('product_name');
            $table->string('category_name');
            $table->string('product_code');
            $table->string('product_color');
            $table->string('product_size');
            $table->string('product_sku');
            $table->float('taxable_amount');
            $table->float('product_gst');
            $table->float('gst_percent');
            $table->float('mrp');
            $table->float('final_price');
            $table->float('discount');
            $table->float('discount_amount');
            $table->float('product_discount_amount');
            $table->string('discount_type');
            $table->float('product_price');
            $table->integer('product_qty');
            $table->float('sub_total');
            $table->float('grand_total');
            $table->string('coupon_used');
            $table->float('discount_rate');
            $table->float('line_discount');
            $table->float('unit_price');
            $table->float('line_amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders_products');
    }
};

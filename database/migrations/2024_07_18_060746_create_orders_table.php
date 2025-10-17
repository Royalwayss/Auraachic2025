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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('coupon_code');
            $table->float('coupon_discount');
            $table->float('shipping_charges');
            $table->float('cod_charges');
            $table->float('taxes');
            $table->float('sub_total');
            $table->float('grand_total');
            $table->string('payment_method');
            $table->string('payment_gateway');
            $table->string('payment_status');
            $table->text('payment_response');
            $table->string('delivery_method');
            $table->string('order_status');
            $table->string('awb_number');
            $table->string('invoice_no');
            $table->string('invoice_date');
            $table->float('total_weight');
            $table->string('comments');
            $table->string('ip_address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

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
        Schema::create('orders_addresses', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->string('billing_name');
            $table->string('billing_mobile');
            $table->string('billing_postcode');
            $table->string('billing_address');
            $table->string('billing_country');
            $table->string('billing_state');
            $table->string('billing_city');
            $table->string('shipping_name');
            $table->string('shipping_mobile');
            $table->string('shipping_postcode');
            $table->string('shipping_address');
            $table->string('shipping_country');
            $table->string('shipping_state');
            $table->string('shipping_city');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders_addresses');
    }
};

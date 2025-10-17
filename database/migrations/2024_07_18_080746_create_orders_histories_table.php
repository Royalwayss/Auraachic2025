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
        Schema::create('orders_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->string('order_status');
            $table->text('comments');
            $table->string('awb_number');
            $table->string('invoice_no');
            $table->string('invoice_date');
            $table->string('shipped_by');
            $table->integer('updated_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders_histories');
    }
};

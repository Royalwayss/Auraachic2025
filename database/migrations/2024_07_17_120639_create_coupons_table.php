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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('coupon_option');
            $table->string('coupon_code');
            $table->enum('coupon_applicable_on',['all','discounted','non-discounted']);
            $table->string('categories');
            $table->string('brands');
            $table->text('users');
            $table->string('coupon_type');
            $table->string('amount_type');
            $table->float('amount');
            $table->integer('min_qty');
            $table->integer('max_qty');
            $table->integer('min_amount');
            $table->integer('max_amount');
            $table->date('expiry_date');
            $table->tinyInteger('status');
            $table->tinyInteger('visible');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};

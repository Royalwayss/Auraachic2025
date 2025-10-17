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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id');
            $table->integer('brand_id');
            $table->string('product_name');
            $table->string('product_code');
            $table->string('product_color');
            $table->string('family_color');
            $table->string('group_code')->nullable();
            $table->float('product_price');
            $table->float('product_discount');
            $table->float('product_discount_amount');
            $table->string('discount_type');
            $table->float('product_gst');
            $table->float('final_price');
            $table->string('main_image');
            $table->float('product_weight');
            $table->string('product_video')->nullable();
            $table->text('description')->nullable();
            $table->text('key_features')->nullable();
            $table->text('wash_care')->nullable();
            $table->text('search_keywords')->nullable();
            $table->string('sleeve');
            $table->string('fabric');
            $table->string('neck');
            $table->string('fit');
            $table->string('occasion');
            $table->integer('stock')->nullable();
            $table->integer('product_sort')->nullable();
            $table->integer('no_of_sales')->nullable();
            $table->enum('is_new',['No','Yes']);
            $table->enum('is_featured',['No','Yes']);
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

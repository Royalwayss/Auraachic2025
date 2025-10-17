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
        Schema::create('home_widget_contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedbigInteger('home_widget_id')->index()->nullable();
            $table->string('desktop_image')->nullable();
            $table->string('mobile_image')->nullable();
            $table->string('video')->nullable();
            $table->unsignedbigInteger('category_id')->index()->nullable();
            $table->unsignedbigInteger('product_id')->index()->nullable();
            $table->unsignedbigInteger('brand_id')->index()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_widget_contents');
    }
};

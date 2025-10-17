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
        Schema::create('home_widgets', function (Blueprint $table) {
            $table->id();
            $table->string('type',255);
            $table->unsignedbigInteger('parent_id')->index()->nullable();
            $table->string('heading')->nullable();
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->string('script_code')->nullable();
            $table->integer('sort')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->string('link_to',50)->nullable();
            $table->string('search_term')->nullable();
            $table->unsignedbigInteger('category_id')->index()->nullable();
            $table->unsignedbigInteger('product_id')->index()->nullable();
            $table->unsignedbigInteger('brand_id')->index()->nullable();
            $table->string('section_type')->nullable();
            $table->string('alt_tag')->nullable();
            $table->string('title_tag')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_widgets');
    }
};

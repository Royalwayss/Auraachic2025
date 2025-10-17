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
        Schema::create('users_credits', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('user_email');
            $table->float('amount');
            $table->enum('type',['credit','debit'])->default('credit');
            $table->string('action');
            $table->text('remarks');
            $table->integer('order_id');
            $table->date('expiry_date');
            $table->string('created_by');
            $table->string('ip_address');
            $table->enum('is_expired',['No','Yes']);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_credits');
    }
};

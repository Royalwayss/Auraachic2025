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
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->after('name');
            $table->string('last_name')->after('first_name');
            $table->string('address')->after('last_name');
            $table->string('city')->after('address');
            $table->string('state')->after('city');
            $table->string('country')->after('state');
            $table->string('pincode')->after('country');
            $table->string('mobile')->after('pincode');
            $table->integer('credit')->after('password');
            $table->tinyInteger('status')->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('address');
            $table->dropColumn('city');
            $table->dropColumn('state');
            $table->dropColumn('country');
            $table->dropColumn('pincode');
            $table->dropColumn('mobile');
            $table->dropColumn('credit');
            $table->dropColumn('status');
        });
    }
};

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
        Schema::table('car_checks', function (Blueprint $table) {
            $table->unsignedBigInteger('car_check_category_id')->nullable();
            $table->unsignedBigInteger('car_check_sub_category_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_checks', function (Blueprint $table) {
            //
        });
    }
};

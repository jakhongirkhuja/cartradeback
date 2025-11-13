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
        Schema::table('cars', function (Blueprint $table) {
            $table->text('type')->default('sale'); //rent
            $table->boolean('rent_status')->default(true);
            $table->integer('rent_price')->nullable();
            $table->integer('rent_initial_price')->nullable();
            $table->integer('rent_deposit')->nullable();
            $table->text('engine_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            //
        });
    }
};

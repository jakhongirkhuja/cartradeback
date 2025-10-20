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
        Schema::create('car_check_sub_categories', function (Blueprint $table) {
            $table->id();
            $table->text('type')->default('ice');
            $table->integer('order')->default(1);
            $table->unsignedBigInteger('car_check_category_id');
            $table->string('title_ru');
            $table->string('title_uz')->nullable();
            $table->string('title_en')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_check_sub_categories');
    }
};

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
        Schema::create('enqueries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mark_id')->nullable();
            $table->unsignedBigInteger('car_model_id')->nullable();
            $table->string('name')->nullable();
            $table->string('familyName')->nullable();
            $table->string('phoneNumber');
            $table->string('email')->nullable();
            $table->string('type')->default('guest');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enqueries');
    }
};

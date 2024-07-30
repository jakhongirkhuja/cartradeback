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
        Schema::create('auksions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->timestamp('time_start');
            $table->timestamp('time_end');
            $table->bigInteger('current_price')->default(0)->index();
            $table->boolean('status')->default(false);
            $table->string('key');
            $table->bigInteger('sold_price')->default(0);
            $table->unsignedBigInteger('buy_user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auksions');
    }
};

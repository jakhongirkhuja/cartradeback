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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->text('order_number')->nullable();
            $table->foreignId('car_id')->constrained('cars');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->integer('total_price');
            $table->integer('payed_price')->default(0);
            $table->text('status')->default('pending'); //payment//
            $table->text('rent_status')->nullable(); //accepted,car_given,in_use,completed,rejected
            $table->text('fiscalUrl')->nullable();
            $table->text('comment')->nullable();
            $table->text('owner_signature')->nullable();
            $table->text('client_signature')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};

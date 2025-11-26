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
        Schema::create('booking_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->boolean('save')->default(false);
            $table->integer('step')->default(1);
            $table->boolean('accept')->nullable();
            $table->text('comment')->nullable();
            $table->json('images')->nullable();
            $table->text('text')->nullable(); //probeg

            // $table->string('title')->nullable();
            // $table->text('text')->nullable();
            // $table->json('data')->nullable();
            // $table->json('photo')->nullable();
            // $table->text('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_histories');
    }
};

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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->text('title')->index();
            $table->longText('body');
            $table->jsonb('functions')->nullable();
            $table->text('start_price')->default(0)->index();
            $table->text('buy_price')->default(0)->index();
            $table->text('images')->nullable();
            $table->unsignedBigInteger('mark_id')->index();
            $table->unsignedBigInteger('car_model_id')->index();
            $table->unsignedBigInteger('car_color_id')->index();
            $table->unsignedBigInteger('transmission_id')->index();
            $table->unsignedBigInteger('car_condtion_id')->index();
            $table->unsignedBigInteger('body_type_id')->index();
            $table->unsignedBigInteger('fuil_type_id')->index();
            $table->text('drive_types')->default('2WD')->index();
            $table->integer('year')->index();
            $table->text('mileage')->default(0)->index();
            $table->double('engine_capacity')->default(1.0);
            $table->integer('doors')->default(5);
            $table->integer('cylinders')->default(10);
            $table->text('vin')->index();
            $table->unsignedBigInteger('auksion_id')->index();
            $table->integer('salon')->default(5);
            $table->integer('engine')->default(5);
            $table->integer('carbody')->default(5);
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};

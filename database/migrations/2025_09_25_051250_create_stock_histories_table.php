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
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('batch_id');
            $table->unsignedBigInteger('balance_after');
            $table->unsignedBigInteger('stock_movement_type_id')->comment('1-increment, 2-decrement, 3-adjustment');
            $table->unsignedBigInteger('stock_movement_referance_id')->comment('1-sold, 2-purchased, 3-returned, 4-expired');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('batch_id')->references('id')->on('batches');
            $table->foreign('stock_movement_type_id')->references('id')->on('stock_movement_types');
            $table->foreign('stock_movement_referance_id')->references('id')->on('stock_movement_referances');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_histories');
    }
};

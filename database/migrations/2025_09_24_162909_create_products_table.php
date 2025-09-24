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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('retailer_id');
            $table->string('name');
            $table->string('code')->unique();
            $table->string('barcode')->nullable();
            $table->boolean('status')->dafault(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('retailer_id')->references('id')->on('product_retailers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

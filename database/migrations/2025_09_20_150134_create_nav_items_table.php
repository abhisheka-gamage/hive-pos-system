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
        Schema::create('nav_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nav_header_id');
            $table->string('display_name')->nullable();
            $table->string('code')->unique();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('nav_header_id')->references('id')->on('nav_headers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nav_items');
    }
};

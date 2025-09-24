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
        Schema::create('nav_sub_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nav_item_id');
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('nav_type_id');
            $table->string('display_name')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('nav_item_id')->references('id')->on('nav_items');
            $table->foreign('permission_id')->references('id')->on('permissions');
            $table->foreign('nav_type_id')->references('id')->on('nav_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nav_sub_items');
    }
};

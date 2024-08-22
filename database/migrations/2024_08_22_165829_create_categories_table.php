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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // mobile-phones => uri => categories/4 => categories/mobile-phones
            $table->string('name')->unique(); // Mobile Phones
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();

            $table->index(['parent_id']);
            $table->fullText(['slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};

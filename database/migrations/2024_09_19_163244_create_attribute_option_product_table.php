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
        Schema::create('attribute_option_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_option_id')->constrained('attribute_options')
                ->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')
                ->cascadeOnDelete();
            $table->unsignedSmallInteger('quantity')->default(0);
            $table->float('price')->unsigned()->startingValue(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_option_product');
    }
};

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
            $table->string('slug')->unique();
            $table->string('title')->unique();
            $table->string('SKU', 35)->unique(); // Serial number
            $table->text('description')->nullable();
            $table->float('price')->unsigned()->startingValue(1);
            $table->unsignedTinyInteger('discount')->nullable();
            $table->unsignedSmallInteger('quantity')->default(0);
            $table->tinyText('thumbnail');
            $table->timestamps();

            $table->fullText(['slug']); // ..../products/tv-lg.. => obj Model Product
            $table->fullText(['title']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropFullText(['slug']);
            $table->dropFullText(['title']);
        });
        Schema::dropIfExists('products');
    }
};

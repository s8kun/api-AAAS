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
        $table->string('name');
        $table->string('slug')->unique();
        $table->decimal('original_price', 10, 2);
        $table->decimal('price', 10, 2);
        $table->integer('discount_percentage')->default(0);
        $table->integer('total_discount_percentage')->default(0);
        $table->decimal('savings', 10, 2)->default(0);
        $table->string('size')->nullable();

        $table->string('exterior_image')->nullable();
        $table->json('interior_images')->nullable();

        $table->json('categories')->nullable();
        $table->string('category');

        $table->json('tags')->nullable();

        $table->boolean('in_stock')->default(true);
        $table->boolean('featured')->default(false);

        $table->longText('description')->nullable();
        $table->json('benefits')->nullable();
        $table->json('ingredients')->nullable();
        $table->longText('how_to_use')->nullable();
        $table->json('warnings')->nullable();

        $table->float('rating')->default(0);
        $table->integer('reviews')->default(0);
        $table->integer('sold_count')->default(0);
        
        $table->timestamps();
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

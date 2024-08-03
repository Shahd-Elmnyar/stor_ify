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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');
            $table->double('price');
            $table->double('total');
            $table->foreignId('size_id')->constrained()->onDelete('cascade')->onUpdate('cascade')->default(1);
            $table->foreignId('color_id')->constrained()->onDelete('cascade')->onUpdate('cascade')->default(1);
            $table->foreignId('cart_id')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart__items');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('slug', 255);
            $table->string('sku', 255);
            $table->string('barcode', 255)->nullable();
            $table->text('description')->nullable();
            $table->decimal('purchase_price', 10, 2)->default(0.00);
            $table->decimal('sale_price', 10, 2)->default(0.00);
            $table->integer('stock')->default(0);
            $table->integer('min_stock')->default(5);
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('image', 255)->nullable();
            $table->boolean('active')->default(1);
            $table->boolean('featured')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
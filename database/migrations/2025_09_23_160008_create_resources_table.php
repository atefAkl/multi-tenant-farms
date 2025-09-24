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
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 50)->nullable();
            $table->string('name', 255);
            $table->string('category', 100)->nullable();
            $table->string('unit', 50)->nullable();
            $table->decimal('stock_qty', 10, 3)->default(0);
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->decimal('selling_price', 10, 2)->nullable();
            $table->string('barcode', 100)->nullable();
            $table->string('location', 100)->nullable();
            $table->decimal('min_stock_level', 10, 3)->nullable();
            $table->decimal('max_stock_level', 10, 3)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};

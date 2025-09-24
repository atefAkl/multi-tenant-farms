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
        Schema::create('farms', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('location');
            $table->string('owner', 255)->nullable();
            $table->decimal('size', 10, 2)->nullable(); // in hectares
            $table->text('description')->nullable();
            $table->string('coordinates', 100)->nullable(); // GPS coordinates
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farms');
    }
};

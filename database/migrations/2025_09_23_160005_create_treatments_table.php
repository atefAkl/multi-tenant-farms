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
        Schema::create('treatments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('palm_tree_id');
            $table->unsignedBigInteger('worker_id');
            $table->date('treatment_date');
            $table->string('treatment_type', 100);
            $table->text('description')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->enum('effectiveness', ['excellent', 'good', 'fair', 'poor'])->nullable();
            $table->timestamps();

            $table->foreign('palm_tree_id')->references('id')->on('palm_trees')->onDelete('cascade');
            $table->foreign('worker_id')->references('id')->on('workers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatments');
    }
};

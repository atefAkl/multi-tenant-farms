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
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('palm_tree_id');
            $table->unsignedBigInteger('worker_id');
            $table->date('inspection_date');
            $table->text('notes')->nullable();
            $table->enum('health_status', ['excellent', 'good', 'fair', 'poor', 'critical']);
            $table->text('recommendations')->nullable();
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
        Schema::dropIfExists('inspections');
    }
};

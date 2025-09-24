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
        Schema::create('palm_trees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('block_id');
            $table->string('tree_code', 50)->unique();
            $table->integer('row_no')->nullable();
            $table->integer('col_no')->nullable();
            $table->unsignedBigInteger('stage_id');
            $table->string('variety', 100)->nullable();
            $table->date('planting_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'dead'])->default('active');
            $table->timestamps();

            $table->foreign('block_id')->references('id')->on('blocks')->onDelete('cascade');
            $table->foreign('stage_id')->references('id')->on('palm_stages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('palm_trees');
    }
};

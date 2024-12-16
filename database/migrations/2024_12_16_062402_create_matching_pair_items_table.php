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
        Schema::create('matching_pair_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('matching_pair_id'); 
            $table->string('question');       
            $table->string('answer');         
            $table->timestamps();

            $table->foreign('matching_pair_id')->references('id')->on('matching_pairs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matching_pair_items');
    }
};

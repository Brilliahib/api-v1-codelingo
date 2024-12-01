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
        Schema::create('sections', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('material_id') 
                ->constrained('materials') 
                ->onDelete('cascade');
            $table->string('title'); 
            $table->text('content'); 
            $table->integer('order')->default(0); 
            $table->boolean('is_locked')->default(false); 
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};

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
        Schema::create('vectors', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->foreignId('model_id');
            $table->enum('category',  ['MAP', 'WATER_STRESS', 'DROUGHT', 'FLOOD']);
            $table->enum('type',  ['IMAGE', 'SVG']);
            $table->enum('model_type', ['App\\\\Models\\\\Zone', 'App\\\\Models\\\\Report']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vectors');
    }
};

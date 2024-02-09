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
        Schema::create('vector_keys', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->enum('type', ['COLOR', 'IMAGE', 'FIGURE'])->default('COLOR');
            $table->string('name');
            $table->foreignId('vector_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vector_keys');
    }
};

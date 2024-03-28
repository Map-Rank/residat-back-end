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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->string('location');
            $table->string('organized_by');
            $table->text('media');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('sector_id')->references('id')->on('sectors');
            $table->timestamp('published_at');
            $table->dateTime('date_debut');
            $table->dateTime('date_fin');
            $table->boolean('is_valid')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

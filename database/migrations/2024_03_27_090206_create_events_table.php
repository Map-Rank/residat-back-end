<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->text('media')->nullable();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('sector_id')->references('id')->on('sectors');
            $table->timestamp('published_at');
            $table->timestamp('date_debut')->default(DB::raw('now()'));
            $table->timestamp('date_fin');
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

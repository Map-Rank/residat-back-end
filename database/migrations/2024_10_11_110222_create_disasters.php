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
        Schema::create('disasters', function (Blueprint $table) {
            $table->id();
            $table->text('description')->nullable();
            $table->string('locality');
            $table->double('latitude');
            $table->double('longitude');
            $table->text('image')->nullable();
            $table->foreignId('zone_id');
            $table->integer('level')->default(1);
            $table->enum('type', ['FLOOD', 'DROUGHT']);
            $table->timestampTz('start_period')->nullable();
            $table->timestampTz('end_period')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disasters');
    }
};

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
        Schema::create('metric_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('hazard', ['DEGREE_OF_IMPACT', 'CLIMATE_VULNERABILITY', 'CLIMATE_RISK_THREATS']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metric_types');
    }
};

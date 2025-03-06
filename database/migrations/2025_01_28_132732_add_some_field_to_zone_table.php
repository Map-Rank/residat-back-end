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
        Schema::table('zones', function (Blueprint $table) {
            $table->float('hist_precipation')->nullable();
            $table->float('hist_temperature')->nullable();
            $table->float('hist_std_rain')->nullable();
            $table->float('hist_std_temp')->nullable();
            $table->integer('max_possible_sunshine_hrs')->nullable();
            $table->float('dist_to_river')->nullable();
            $table->enum('soil_type', ['SANDY', 'CLAY', 'LOAMY', 'SLIKY','ROCKY'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zones', function (Blueprint $table) {
            $table->dropColumn([
                'soil_type',
                'hist_precipitation',
                'hist_temperature',
                'hist_std_rain',
                'hist_std_temp',
                'max_possible_sunshine_hrs',
                'dist_to_river',
            ]);
        });
    }
};

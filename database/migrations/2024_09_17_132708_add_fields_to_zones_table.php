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
        if (Schema::hasTable('zones') && !Schema::hasColumns('zones', ['latitude','longitude','geojson'])) {
            Schema::table('zones', function (Blueprint $table) {
                $table->double('latitude')->nullable();
                $table->double('longitude')->nullable();
                $table->text('geojson')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zones', function (Blueprint $table) {
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
            $table->dropColumn('geojson');
        });
    }
};

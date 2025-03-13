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
        Schema::table('predictions', function (Blueprint $table) {
            $table->json('d1_risk')->change();
            $table->json('d2_risk')->nullable()->change();
            $table->json('d3_risk')->nullable()->change();
            $table->json('d4_risk')->nullable()->change();
            $table->json('d5_risk')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('predictions', function (Blueprint $table) {
            $table->text('d1_risk')->change();
            $table->text('d2_risk')->change();
            $table->text('d3_risk')->change();
            $table->text('d4_risk')->change();
            $table->text('d5_risk')->change();
        });
    }
};

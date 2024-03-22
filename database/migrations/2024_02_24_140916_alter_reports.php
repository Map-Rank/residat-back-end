<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     /**
     * Run the migrations.
     */
    public function up()
    {
        if (Schema::hasTable('reports')) {
            if (Schema::hasColumn('reports', 'image')) {
                Schema::table('reports', function (Blueprint $table) {
                    $table->dropColumn('image');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('image')->nullable();
        });
    }
};

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
        if (Schema::hasTable('users') && !Schema::hasColumns('users', ['date_of_birth', 'last_name'])) {
            Schema::table('users', function (Blueprint $table) {
                $table->date('date_of_birth')->nullable()->change();
                $table->string('last_name')->nullable()->change();

            });
         }

        Schema::table('users', function (Blueprint $table) {
            $table->string('language')->default('en')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable(false)->change();
            $table->string('last_name')->nullable(false)->change();

            $table->dropColumn('language');
        });
    }
};

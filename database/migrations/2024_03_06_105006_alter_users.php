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
        if (Schema::hasTable('users')) {
            if (!Schema::hasColumn('users', 'type')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->enum('type', ['COUNCIL', 'DEFAULT'])->default('DEFAULT');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

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
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('owner_name');
            $table->dropColumn('official_document');

            $table->enum('type', ['national', 'regional', 'divisional', 'subdivisional'])->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->text('owner_name')->after('company_name');
            $table->text('official_document')->nullable()->after('profile');

            $table->dropColumn('type');
        });
    }
};

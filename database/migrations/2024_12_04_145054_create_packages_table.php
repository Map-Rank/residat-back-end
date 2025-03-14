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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name_fr');
            $table->string('name_en');
            $table->enum('level', ['National', 'Regional', 'Divisional', 'Subdivisional']);
            $table->enum('periodicity', ['Month', 'Quarter', 'Half', 'Annual']);
            $table->integer('price');
            $table->text('description_fr')->nullable();
            $table->text('description_en')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};

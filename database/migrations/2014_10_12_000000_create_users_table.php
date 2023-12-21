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
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('first_name');
                $table->string('last_name');
                $table->string('email')->unique();
                $table->string('phone');
                $table->date('date_of_birth');
                $table->string('avatar')->nullable()->default('/storage/media/profile.png');
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->boolean('active')->default(0);
                $table->timestamp('activated_at')->nullable();
                $table->boolean('verified')->default(0);
                $table->timestamp('verified_at')->nullable();
                $table->enum('gender', ['male', 'female'])->nullable();
                $table->foreignId('zone_id')->references('id')->on('zones')->onDelete('cascade');
                $table->rememberToken();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

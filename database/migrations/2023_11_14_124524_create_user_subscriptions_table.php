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
        if (!Schema::hasTable('user_subscriptions'))        {
            Schema::create('user_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->double('price');
                $table->timestamp('start_at');
                $table->timestamp('end_at')->nullable();
                $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreignId('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade');
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
        Schema::dropIfExists('user_subscriptions');
    }
};

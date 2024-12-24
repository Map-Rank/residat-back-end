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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('currency', ['XAF'])->default('XAF');
            $table->string('transaction_id')->unique();
            $table->enum('payment_method', ['mobile_money', 'bank_transfer', 'cash', 'online'])->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->dateTime('payment_date');
            $table->text('payment_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

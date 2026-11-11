<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjualan_id')->constrained('penjualan')->cascadeOnDelete();
            $table->string('provider');
            $table->string('order_id')->unique();
            $table->string('status')->default('pending');
            $table->string('fraud_status')->nullable();
            $table->string('payment_type')->nullable();
            $table->decimal('gross_amount', 15, 2)->default(0);
            $table->json('payload')->nullable();
            $table->timestamp('transaction_time')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

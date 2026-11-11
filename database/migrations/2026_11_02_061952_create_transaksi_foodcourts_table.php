<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_foodcourts', function (Blueprint $table) {
            $table->id();
            $table->dateTime('tanggal');
            $table->decimal('total', 14, 2)->default(0);
            $table->string('metode')->default('Tunai');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_foodcourts');
    }
};

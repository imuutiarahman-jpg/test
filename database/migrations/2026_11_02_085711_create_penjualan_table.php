<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('pelanggan');
            $table->text('item_pesanan');
            $table->string('metode_pembayaran');
            $table->date('jatuh_tempo')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('kategori_lokasi');
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->string('status_pembayaran')->default('Belum Bayar');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};

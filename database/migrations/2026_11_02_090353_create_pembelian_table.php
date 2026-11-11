<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembelian', function (Blueprint $table) {
            $table->id();
            $table->string('no_invoice');
            $table->string('nama_supplier');
            $table->text('keterangan')->nullable();
            $table->date('tgl_beli');
            $table->date('jatuh_tempo')->nullable();
            $table->string('lokasi_penyimpanan');
            $table->string('status_pembayaran')->default('Belum Bayar');
            $table->string('akun_pembayaran')->nullable();
            $table->decimal('ongkir', 15, 2)->default(0);
            $table->decimal('total_biaya', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelian');
    }
};

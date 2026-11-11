<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_qmas', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->integer('stok_masuk_gl_19l')->default(0);
            $table->integer('stok_masuk_gelas_220ml')->default(0);
            $table->integer('stok_masuk_botol_330ml')->default(0);
            $table->integer('stok_masuk_botol_600ml')->default(0);
            $table->integer('stok_masuk_botol_15l')->default(0);
            $table->integer('stok_keluar_gl_19l')->default(0);
            $table->integer('stok_keluar_gelas_220ml')->default(0);
            $table->integer('stok_keluar_botol_330ml')->default(0);
            $table->integer('stok_keluar_botol_600ml')->default(0);
            $table->integer('stok_keluar_botol_15l')->default(0);
            $table->integer('sisa_stok_gl_19l')->default(0);
            $table->integer('sisa_stok_gelas_220ml')->default(0);
            $table->integer('sisa_stok_botol_330ml')->default(0);
            $table->integer('sisa_stok_botol_600ml')->default(0);
            $table->integer('sisa_stok_botol_15l')->default(0);
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_qmas');
    }
};

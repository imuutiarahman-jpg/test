<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aset_tetap', function (Blueprint $table) {
            $table->id();
            $table->string('nama_aset');
            $table->date('tgl_beli');
            $table->decimal('nilai_perolehan', 15, 2)->default(0);
            $table->integer('umur')->default(1);
            $table->decimal('susut_per_bulan', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aset_tetap');
    }
};

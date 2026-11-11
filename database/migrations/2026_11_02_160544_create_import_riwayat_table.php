<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_riwayat', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun_default')->default(date('Y'));
            $table->string('sumber_data');
            $table->longText('konten_mentah')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_riwayat');
    }
};

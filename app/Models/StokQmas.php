<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokQmas extends Model
{
    protected $fillable = [
        'tanggal',
        'stok_masuk_gl_19l', 'stok_masuk_gelas_220ml', 'stok_masuk_botol_330ml', 'stok_masuk_botol_600ml', 'stok_masuk_botol_15l',
        'stok_keluar_gl_19l', 'stok_keluar_gelas_220ml', 'stok_keluar_botol_330ml', 'stok_keluar_botol_600ml', 'stok_keluar_botol_15l',
        'sisa_stok_gl_19l', 'sisa_stok_gelas_220ml', 'sisa_stok_botol_330ml', 'sisa_stok_botol_600ml', 'sisa_stok_botol_15l',
        'keterangan', 'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

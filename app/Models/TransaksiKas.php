<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiKas extends Model
{
    protected $table = 'transaksi_kas';

    protected $fillable = ['jenis_transaksi', 'akun_sumber_id', 'akun_tujuan_id', 'nominal', 'keterangan', 'tanggal'];

    protected $casts = ['tanggal' => 'date'];

    public function sumber(): BelongsTo
    {
        return $this->belongsTo(AkunKas::class, 'akun_sumber_id');
    }

    public function tujuan(): BelongsTo
    {
        return $this->belongsTo(AkunKas::class, 'akun_tujuan_id');
    }
}

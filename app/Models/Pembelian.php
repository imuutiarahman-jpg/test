<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pembelian extends Model
{
    protected $table = 'pembelian';

    protected $fillable = [
        'no_invoice', 'nama_supplier', 'keterangan', 'tgl_beli', 'jatuh_tempo',
        'lokasi_penyimpanan', 'status_pembayaran', 'akun_pembayaran', 'ongkir', 'total_biaya',
    ];

    protected $casts = [
        'tgl_beli' => 'date',
        'jatuh_tempo' => 'date',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(DetailPembelian::class);
    }
}

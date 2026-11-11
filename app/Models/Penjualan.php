<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualan';

    protected $fillable = [
        'tanggal', 'pelanggan', 'item_pesanan', 'metode_pembayaran',
        'jatuh_tempo', 'no_hp', 'kategori_lokasi', 'total_harga', 'status_pembayaran',
        'payment_provider', 'payment_reference', 'payment_url', 'paid_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jatuh_tempo' => 'date',
        'paid_at' => 'datetime',
    ];
}

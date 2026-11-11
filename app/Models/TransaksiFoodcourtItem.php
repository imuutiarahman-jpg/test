<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiFoodcourtItem extends Model
{
    protected $table = 'transaksi_foodcourt_items';

    protected $fillable = ['transaksi_foodcourt_id', 'menu_id', 'qty', 'harga_satuan', 'subtotal'];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(TransaksiFoodcourt::class, 'transaksi_foodcourt_id');
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}

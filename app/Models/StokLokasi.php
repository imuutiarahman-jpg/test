<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokLokasi extends Model
{
    protected $table = 'stok_lokasi';

    protected $fillable = ['produk_id', 'daqam', 'food_court', 'kantor', 'min_stok'];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function total(): int
    {
        return (int) $this->daqam + (int) $this->food_court + (int) $this->kantor;
    }
}

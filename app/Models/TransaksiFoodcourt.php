<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransaksiFoodcourt extends Model
{
    protected $table = 'transaksi_foodcourts';

    protected $fillable = ['tanggal', 'total', 'metode', 'user_id'];

    protected $casts = [
        'tanggal' => 'datetime',
        'total' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(TransaksiFoodcourtItem::class, 'transaksi_foodcourt_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

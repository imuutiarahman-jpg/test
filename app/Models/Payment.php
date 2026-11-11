<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'penjualan_id', 'provider', 'order_id', 'status', 'fraud_status',
        'payment_type', 'gross_amount', 'payload', 'transaction_time',
    ];

    protected $casts = [
        'payload' => 'array',
        'transaction_time' => 'datetime',
        'gross_amount' => 'decimal:2',
    ];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }
}

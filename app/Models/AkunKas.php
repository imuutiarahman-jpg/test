<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AkunKas extends Model
{
    protected $table = 'akun_kas';

    protected $fillable = ['nama_kas', 'uraian', 'saldo_awal', 'saldo_saat_ini'];

    public function transaksiSumber(): HasMany
    {
        return $this->hasMany(TransaksiKas::class, 'akun_sumber_id');
    }

    public function transaksiTujuan(): HasMany
    {
        return $this->hasMany(TransaksiKas::class, 'akun_tujuan_id');
    }
}

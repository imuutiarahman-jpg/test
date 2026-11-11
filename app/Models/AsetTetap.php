<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsetTetap extends Model
{
    protected $table = 'aset_tetap';

    protected $fillable = ['nama_aset', 'tgl_beli', 'nilai_perolehan', 'umur', 'susut_per_bulan'];

    protected $casts = [
        'tgl_beli' => 'date',
        'nilai_perolehan' => 'decimal:2',
        'susut_per_bulan' => 'decimal:2',
    ];
}

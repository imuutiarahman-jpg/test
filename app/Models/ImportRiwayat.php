<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportRiwayat extends Model
{
    protected $table = 'import_riwayat';

    protected $fillable = ['tahun_default', 'sumber_data', 'konten_mentah'];
}

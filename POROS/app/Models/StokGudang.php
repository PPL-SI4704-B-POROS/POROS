<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokGudang extends Model
{
    protected $table = 'stok_gudang';

    protected $fillable = [
        'nama_bahan',
        'jumlah_masuk',
        'satuan',
        'tanggal_terima',
        'keterangan',
    ];
}
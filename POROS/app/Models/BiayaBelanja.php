<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaBelanja extends Model
{
    protected $table = 'biaya_belanja'; // Pastikan sesuai nama tabel di migrasi
    protected $fillable = [
        'bahan_baku_id', 
        'supplier_id', 
        'jumlah_beli', 
        'total_harga', 
        'tanggal_belanja'
    ];
}

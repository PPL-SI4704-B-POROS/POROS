<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama_menu',
        'total_kalori',
        'total_protein',
        'total_karbohidrat',
        'total_lemak',
        'deskripsi_gizi',
    ];

    public function reseps()
    {
        return $this->hasMany(Resep::class);
    }

    public function produksiHarians()
    {
        return $this->hasMany(ProduksiHarian::class);
    }
}

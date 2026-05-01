<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KatalogPangan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kode_tkpi',
        'nama_pangan',
        'kategori',
        'sumber',
        'energi_per_100g',
        'protein_per_100g',
        'lemak_per_100g',
        'karbohidrat_per_100g',
        'serat_per_100g',
        'kalsium_per_100g',
        'besi_per_100g',
        'bdd_persen',
    ];

    public function bahanBakus()
    {
        return $this->hasMany(BahanBaku::class);
    }
}

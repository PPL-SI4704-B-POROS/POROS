<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BahanBaku extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama_bahan',
        'satuan',
        'energi_per_100g',
        'protein_per_100g',
        'karbohidrat_per_100g',
        'lemak_per_100g',
        'stok',
        'stok_minimal',
        'supplier_id',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    // ---- Accessors for per‑gram nutrient values ----
    public function getEnergiPerGramAttribute()
    {
        return $this->energi_per_100g / 100;
    }

    public function getProteinPerGramAttribute()
    {
        return $this->protein_per_100g / 100;
    }

    public function getKarbohidratPerGramAttribute()
    {
        return $this->karbohidrat_per_100g / 100;
    }

    public function getLemakPerGramAttribute()
    {
        return $this->lemak_per_100g / 100;
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BahanBaku extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama_bahan',
        'katalog_pangan_id',
        'supplier_id',
        'satuan',
        'stok',
        'stok_minimal',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function katalogPangan()
    {
        return $this->belongsTo(KatalogPangan::class);
    }

    public function plateWastes()
    {
        return $this->hasMany(PlateWaste::class);
    }

    public function menuBahanBakus()
    {
        return $this->hasMany(MenuBahanBaku::class);
    }

    // Accessors for Nutrition Data via KatalogPangan
    public function getEnergiPer100gAttribute()
    {
        return $this->katalogPangan ? $this->katalogPangan->energi_per_100g : 0;
    }

    public function getProteinPer100gAttribute()
    {
        return $this->katalogPangan ? $this->katalogPangan->protein_per_100g : 0;
    }

    public function getKarbohidratPer100gAttribute()
    {
        return $this->katalogPangan ? $this->katalogPangan->karbohidrat_per_100g : 0;
    }

    public function getLemakPer100gAttribute()
    {
        return $this->katalogPangan ? $this->katalogPangan->lemak_per_100g : 0;
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

    public function getSeratPer100gAttribute()
    {
        return $this->katalogPangan ? $this->katalogPangan->serat_per_100g : 0;
    }

    public function getKalsiumPer100gAttribute()
    {
        return $this->katalogPangan ? $this->katalogPangan->kalsium_per_100g : 0;
    }

    public function getBesiPer100gAttribute()
    {
        return $this->katalogPangan ? $this->katalogPangan->besi_per_100g : 0;
    }

    public function getSeratPerGramAttribute()
    {
        return $this->serat_per_100g / 100;
    }

    public function getKalsiumPerGramAttribute()
    {
        return $this->kalsium_per_100g / 100;
    }

    public function getBesiPerGramAttribute()
    {
        return $this->besi_per_100g / 100;
    }
}


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

    public function formHargas()
    {
        return $this->hasMany(FormHarga::class, 'bahan_id');
    }

    public function getHargaTerbaruAttribute()
    {
        $latest = $this->formHargas()
            ->where('supplier_id', $this->supplier_id)
            ->orderBy('tanggal_update', 'desc')
            ->first();
        return $latest ? (float) $latest->harga_per_gram : 0.0;
    }

    public function getHargaSatuanTerbaruAttribute()
    {
        $latest = $this->formHargas()
            ->where('supplier_id', $this->supplier_id)
            ->orderBy('tanggal_update', 'desc')
            ->first();
        return $latest ? (float) $latest->harga_satuan : 0.0;
    }

    public function getSatuanHargaTerbaruAttribute()
    {
        $latest = $this->formHargas()
            ->where('supplier_id', $this->supplier_id)
            ->orderBy('tanggal_update', 'desc')
            ->first();
        return $latest ? $latest->satuan_harga : $this->satuan;
    }

    public function getStokFormattedAttribute()
    {
        $stok = $this->stok;
        $satuan = strtolower(trim($this->satuan));
        
        if ($satuan === 'gram' && $stok >= 1000) {
            return ($stok / 1000) . ' kg';
        }
        if ($satuan === 'ml' && $stok >= 1000) {
            return ($stok / 1000) . ' liter';
        }
        return $stok . ' ' . $this->satuan;
    }

    public function getStokMinimalFormattedAttribute()
    {
        $stok = $this->stok_minimal;
        $satuan = strtolower(trim($this->satuan));
        
        if ($satuan === 'gram' && $stok >= 1000) {
            return ($stok / 1000) . ' kg';
        }
        if ($satuan === 'ml' && $stok >= 1000) {
            return ($stok / 1000) . ' liter';
        }
        return $stok . ' ' . $this->satuan;
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


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StokGudang extends Model
{
    use HasFactory;

    protected $table = 'stok_gudang';

    protected $fillable = [
        'bahan_baku_id',
        'supplier_id',
        'quantity',
        'satuan',
    ];

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function histories()
    {
        return $this->hasMany(StockHistory::class, 'stok_gudang_id')
                    ->orderByDesc('incoming_date');
    }

    // 'critical' | 'low' | 'good'
    public function getStockLevelAttribute(): string
    {
        if ($this->quantity <= 10) return 'critical';
        if ($this->quantity <= 30) return 'low';
        return 'good';
    }
}   
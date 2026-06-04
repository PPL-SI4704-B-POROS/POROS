<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormHarga extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'form_hargas';

    protected $fillable = [
        'harga_satuan',
        'satuan_harga',
        'tanggal_update',
        'supplier_id',
        'bahan_id',
    ];

    protected $casts = [
        'harga_satuan' => 'float',
        'tanggal_update' => 'date',
    ];

    public function getHargaPerGramAttribute()
    {
        $satuan = trim(strtolower($this->satuan_harga));
        
        // Konversi kg & liter (dibagi 1000 untuk dapat harga per gram/ml)
        if (in_array($satuan, ['kg', 'kilogram', 'kilo', 'liter', 'l'])) {
            return (float) ($this->harga_satuan / 1000);
        }
        
        // Konversi kemasan 100g & 100ml
        if (in_array($satuan, ['100g', '100ml', '100 gram'])) {
            return (float) ($this->harga_satuan / 100);
        }
        
        // Default untuk gram, ml, butir, pcs, pack, dll. (tidak memerlukan konversi)
        return (float) $this->harga_satuan;
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_id');
    }
}

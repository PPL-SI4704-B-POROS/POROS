<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaBelanja extends Model
{
    protected $table = 'biaya_belanja';

    protected $fillable = [
        'bahan_baku_id',
        'supplier_id',
        'dapur_id',
        'jumlah_beli',
        'total_harga',
        'tanggal_belanja',
    ];

    /**
     * Get the kitchen (dapur) associated with the purchase.
     */
    public function dapur()
    {
        return $this->belongsTo(User::class, 'dapur_id');
    }

    /**
     * Get the ingredient (bahan baku) associated with the purchase.
     */
    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }

    /**
     * Get the supplier associated with the purchase.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}

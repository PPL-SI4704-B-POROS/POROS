<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockHistory extends Model
{
    use HasFactory;

    protected $table = 'stock_histories';

    protected $fillable = [
        'stok_gudang_id',
        'status',
        'quantity',
        'incoming_date',
        'batch_id',
        'expired_date',
    ];

    protected $casts = [
        'incoming_date' => 'date',
        'expired_date'  => 'date',
    ];

    public function stokGudang()
    {
        return $this->belongsTo(StokGudang::class, 'stok_gudang_id');
    }
}
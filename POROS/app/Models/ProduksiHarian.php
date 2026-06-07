<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProduksiHarian extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tanggal_produksi',
        'total_target_porsi',
        'status_produksi',
        'menu_id',
    ];

    protected $casts = [
        'tanggal_produksi' => 'date',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
    //harga modal keseluruhan
    public function getHargaTotalModalAttribute()
    {
        return $this->menu ? $this->menu->harga_modal_per_porsi * $this->total_target_porsi : 0;
    }
}

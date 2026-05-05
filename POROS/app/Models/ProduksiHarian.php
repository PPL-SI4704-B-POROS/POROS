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
}

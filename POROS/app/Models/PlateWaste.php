<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlateWaste extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'jumlah_waste',
        'tanggal',
        'keterangan',
        'sekolah_id',
        'pengiriman_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_waste' => 'decimal:2',
    ];

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }
}

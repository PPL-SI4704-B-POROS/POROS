<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengiriman extends Model
{
    use SoftDeletes;

    protected $table = 'pengirimans';

    protected $fillable = [
        'waktu_berangkat',
        'waktu_sampai',
        'nama_penerima',
        'status_kirim',
        'produksi_id',
        'sekolah_id',
        'kurir_id',
        'keterangan',
        'ompreng_kembali',
        'waste_menu_1',
        'waste_menu_2',
        'waste_menu_3',
    ];

    protected $casts = [
        'waktu_berangkat' => 'datetime',
        'waktu_sampai' => 'datetime',
    ];

    public function produksi(): BelongsTo
    {
        return $this->belongsTo(ProduksiHarian::class, 'produksi_id');
    }

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function kurir(): BelongsTo
    {
        return $this->belongsTo(Kurir::class);
    }
}

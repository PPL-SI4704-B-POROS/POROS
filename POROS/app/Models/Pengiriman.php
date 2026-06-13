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
        'menu_tersisa',
        'jumlah_sisa_ompreng',
        'tanggal_sisa',
    ];

    protected $casts = [
        'waktu_berangkat' => 'datetime',
        'waktu_sampai' => 'datetime',
        'tanggal_sisa' => 'date',
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Antropometri extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'berat_badan',
        'tinggi_badan',
        'tanggal_ukur',
        'siswa_id',
    ];

    protected $casts = [
        'tanggal_ukur' => 'date',
        'berat_badan' => 'decimal:2',
        'tinggi_badan' => 'decimal:2',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}

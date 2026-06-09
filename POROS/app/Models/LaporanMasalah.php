<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanMasalah extends Model
{
    protected $fillable = [
        'judul_masalah',
        'deskripsi',
        'foto_bukti',
        'status',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedIdAttribute()
    {
        return 'LPR-' . str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }
}
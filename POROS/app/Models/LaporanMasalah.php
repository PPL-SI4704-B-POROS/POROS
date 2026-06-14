<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class LaporanMasalah extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'laporan_masalahs';

    protected $fillable = [
        'judul_masalah',
        'kategori',
        'deskripsi',
        'foto_bukti',
        'status',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Format ID jadi LPR-001, LPR-002, dst
    public function getFormattedIdAttribute(): string
    {
        return 'LPR-' . str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }
}
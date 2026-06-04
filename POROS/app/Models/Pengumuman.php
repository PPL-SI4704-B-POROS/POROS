<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Pengumuman extends Model
{
    use HasFactory;
    protected $table = 'pengumuman';
    protected $fillable = ['judul', 'isi', 'user_id'];
    public function pembuat()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
// melakukan pull untuk mendapatkan perubahan terbaru
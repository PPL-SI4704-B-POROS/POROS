<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sekolah extends Model
{
    use SoftDeletes;

    protected $fillable = ['nama_sekolah', 'alamat', 'jumlah_siswa'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}

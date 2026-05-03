<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    protected $table = 'pengumuman';

    protected $fillable = ['judul', 'isi', 'user_id'];

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
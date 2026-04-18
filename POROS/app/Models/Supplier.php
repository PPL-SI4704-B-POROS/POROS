<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama_supplier',
        'alamat',
        'kontak',
    ];

    public function bahanBakus()
    {
        return $this->hasMany(BahanBaku::class);
    }
}

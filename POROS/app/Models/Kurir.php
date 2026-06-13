<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kurir extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama_kurir',
        'no_plat',
        'kontak',
    ];

    public function pengirimans(): HasMany
    {
        return $this->hasMany(Pengiriman::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resep extends Model
{
    public function resep() { 
        return $this->belongsTo(Resep::class); 
    }
}

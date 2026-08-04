<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    protected $fillable = ['user_id', 'karya_id', 'jumlah'];
    public function karya()
    {
        return $this->belongsTo(Karya::class);
    }

    //OPTIONAL
    public function pembeli() { 
        return $this->belongsTo(User::class, 'user_id'); 
    }
}

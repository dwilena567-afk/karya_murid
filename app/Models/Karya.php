<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karya extends Model
{
    protected $fillable = ['user_id', 'judul', 'deskripsi', 'harga', 'stok', 'gambar', 'status_verifikasi'];

    public function pembuat() { 
        return $this->belongsTo(User::class, 'user_id'); 
    }
}

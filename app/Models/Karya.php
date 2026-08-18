<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karya extends Model
{
   protected $fillable = ['user_id', 'judul', 'deskripsi', 'gambar','kategori_id', 'harga', 'stok', 'status_verifikasi'];

    public function pembuat() { 
        return $this->belongsTo(User::class, 'user_id'); 
    }

    public function kategori() {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }


}

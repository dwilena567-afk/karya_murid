<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Karya extends Model
{
    protected $fillable = ['user_id', 'judul', 'deskripsi', 'gambar', 'kategori_id', 'harga', 'stok', 'status_verifikasi'];

    public function gambarTambahan(): HasMany
    {
        return $this->hasMany(KaryaGambar::class);
    }

    public function getSemuaGambarAttribute(): Collection
    {
        return collect($this->gambar ? [$this->gambar] : [])
            ->concat($this->gambarTambahan->pluck('gambar'));
    }

    public function pembuat() { 
        return $this->belongsTo(User::class, 'user_id'); 
    }

    public function kategori() {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function transaksiDetails()
{
    return $this->hasMany(TransaksiDetail::class, 'karya_id');
}


}

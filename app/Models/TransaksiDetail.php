<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiDetail extends Model
{
    protected $fillable = ['transaksi_id', 'karya_id', 'harga_satuan', 'jumlah'];

    public function transaksi() { 
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    public function karya() { 
        return $this->belongsTo(Karya::class, 'karya_id');
    }
}

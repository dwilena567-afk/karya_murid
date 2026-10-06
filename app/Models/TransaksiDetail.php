<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class TransaksiDetail extends Model
{
    use HasFactory;
    protected $fillable = ['transaksi_id', 'karya_id', 'harga_satuan', 'jumlah'];

   public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function karya()
    {
        return $this->belongsTo(Karya::class);
    }
}

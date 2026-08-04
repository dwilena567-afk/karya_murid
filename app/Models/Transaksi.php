<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $fillable = ['order_id', 'user_id', 'gross_amount', 'payment_type', 'transaction_status', 'snap_token'];

    public function pembeli() { 
        return $this->belongsTo(User::class, 'user_id'); 
    }

    public function details() { 
        return $this->hasMany(TransaksiDetail::class); 
    }
}

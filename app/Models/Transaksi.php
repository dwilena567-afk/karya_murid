<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaksi extends Model
{
    use HasFactory;
    protected $fillable = ['order_id', 'user_id', 'gross_amount', 'payment_type', 'transaction_status', 'snap_token'];

    public function pembeli() { 
        return $this->belongsTo(User::class, 'user_id'); 
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details() { 
        return $this->hasMany(TransaksiDetail::class); 
    }
}

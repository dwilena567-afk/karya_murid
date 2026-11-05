<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
   protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'is_read',
    ];

    // Relasi ke User pengirim
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Relasi ke User penerima
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}

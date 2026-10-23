<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KaryaGambar extends Model
{
    protected $fillable = ['karya_id', 'gambar'];

    public function karya(): BelongsTo
    {
        return $this->belongsTo(Karya::class);
    }
}

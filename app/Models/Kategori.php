<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $fillable = ['nama'];

    public function karyas()
    {
        return $this->hasMany(Karya::class, 'kategori_id');
    }
}

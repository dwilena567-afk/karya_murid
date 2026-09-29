<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = [
            ['nama' => 'Lukisan'],
            ['nama' => 'Patung'],
            ['nama' => 'Seni Digital'],
            ['nama' => 'Fotografi'],
            ['nama' => 'Seni Rupa'],
            ['nama' => 'Musik'],
            ['nama' => 'Seni Sastra'],
            ['nama' => 'Software'],
            ['nama' => 'Karya Fisik']
        ];

        foreach ($kategoris as $kategori) {
            Kategori::create($kategori);
        }
    }
}

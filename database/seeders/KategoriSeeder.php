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
            ['nama' => 'Kerajinan Tangan']
        ];

        foreach ($kategoris as $kategori) {
            Kategori::create($kategori);
        }
    }
}

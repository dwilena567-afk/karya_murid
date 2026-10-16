<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Karya;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KaryaSeeder extends Seeder
{
    /**
     * Seed sample artworks for the catalog and verification page.
     */
    public function run(): void
    {
        $murid = User::where('role', 'murid')->orderBy('id')->first()
            ?? User::firstOrCreate(
                ['email' => 'murid.demo@example.test'],
                [
                    'name' => 'Murid Demo',
                    'password' => Str::random(48),
                    'role' => 'murid',
                ]
            );

        $karyas = [
            [
                'judul' => 'Senja di Ujung Desa',
                'deskripsi' => 'Lukisan pemandangan senja dengan warna hangat yang menggambarkan suasana desa.',
                'kategori' => 'Lukisan',
                'harga' => 150000,
                'stok' => 1,
                'status_verifikasi' => 'approved',
            ],
            [
                'judul' => 'Patung Burung dari Kayu',
                'deskripsi' => 'Patung burung sederhana yang dibuat dengan teknik ukir pada kayu bekas.',
                'kategori' => 'Patung',
                'harga' => 85000,
                'stok' => 2,
                'status_verifikasi' => 'approved',
            ],
            [
                'judul' => 'Motif Batik Nusantara',
                'deskripsi' => 'Ilustrasi digital bermotif flora yang terinspirasi dari ragam hias Nusantara.',
                'kategori' => 'Seni Digital',
                'harga' => 50000,
                'stok' => 5,
                'status_verifikasi' => 'approved',
            ],
            [
                'judul' => 'Pagi di Halaman Sekolah',
                'deskripsi' => 'Foto suasana halaman sekolah pada pagi hari setelah hujan.',
                'kategori' => 'Fotografi',
                'harga' => 25000,
                'stok' => 3,
                'status_verifikasi' => 'approved',
            ],
            [
                'judul' => 'Gitar Mini dari Kardus',
                'deskripsi' => 'Karya kerajinan berbentuk gitar mini yang dibuat dari bahan daur ulang.',
                'kategori' => 'Karya Fisik',
                'harga' => 30000,
                'stok' => 1,
                'status_verifikasi' => 'approved',
            ],
            [
                'judul' => 'Puisi untuk Ibu',
                'deskripsi' => 'Kumpulan puisi pendek tentang kasih sayang dan rasa terima kasih kepada ibu.',
                'kategori' => 'Seni Sastra',
                'harga' => 0,
                'stok' => 10,
                'status_verifikasi' => 'approved',
            ],
            [
                'judul' => 'Sketsa Pasar Tradisional',
                'deskripsi' => 'Sketsa suasana pasar tradisional dengan aktivitas penjual dan pembeli.',
                'kategori' => 'Seni Rupa',
                'harga' => 40000,
                'stok' => 1,
                'status_verifikasi' => 'pending',
            ],
            [
                'judul' => 'Lagu Ceria Saat Istirahat',
                'deskripsi' => 'Komposisi lagu pendek dengan irama ceria untuk menemani waktu istirahat.',
                'kategori' => 'Musik',
                'harga' => 20000,
                'stok' => 4,
                'status_verifikasi' => 'pending',
            ],
            [
                'judul' => 'Aplikasi Catatan Belajar',
                'deskripsi' => 'Konsep aplikasi sederhana untuk menyimpan catatan pelajaran harian.',
                'kategori' => 'Software',
                'harga' => 10000,
                'stok' => 8,
                'status_verifikasi' => 'pending',
            ],
            [
                'judul' => 'Vas Bunga dari Tanah Liat',
                'deskripsi' => 'Vas kecil buatan tangan dari tanah liat dengan tekstur alami.',
                'kategori' => 'Patung',
                'harga' => 45000,
                'stok' => 0,
                'status_verifikasi' => 'rejected',
            ],
            
        ];

        foreach ($karyas as $data) {
            $kategori = Kategori::firstOrCreate(['nama' => $data['kategori']]);
            unset($data['kategori']);

            Karya::firstOrCreate(
                [
                    'user_id' => $murid->id,
                    'judul' => $data['judul'],
                ],
                [
                    ...$data,
                    'kategori_id' => $kategori->id,
                    'gambar' => null,
                ]
            );
        }
    }
}

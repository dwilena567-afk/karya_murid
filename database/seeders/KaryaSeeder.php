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
                'judul' => 'Lorem ipsum dolor sit amet',
                'deskripsi' => 'lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'kategori' => 'Software',
                'harga' => 10000,
                'stok' => 8,
                'status_verifikasi' => 'pending',
            ],
            [
                'judul' => 'Bunga Matahari di Musim Hujan',
                'deskripsi' => 'Lukisan bunga matahari yang tetap tumbuh cerah di tengah suasana hujan.',
                'kategori' => 'Lukisan',
                'harga' => 75000,
                'stok' => 2,
                'status_verifikasi' => 'pending',
            ],
            [
                'judul' => 'Topeng Penjaga Hutan',
                'deskripsi' => 'Topeng kayu dengan ukiran daun dan motif alam sebagai simbol penjaga hutan.',
                'kategori' => 'Patung',
                'harga' => 90000,
                'stok' => 1,
                'status_verifikasi' => 'pending',
            ],
            [
                'judul' => 'Poster Hemat Energi',
                'deskripsi' => 'Poster digital berisi ajakan sederhana untuk menghemat energi di rumah dan sekolah.',
                'kategori' => 'Seni Digital',
                'harga' => 15000,
                'stok' => 10,
                'status_verifikasi' => 'pending',
            ],
            [
                'judul' => 'Jejak Kaki di Pantai',
                'deskripsi' => 'Foto jejak kaki di pasir pantai saat matahari terbit.',
                'kategori' => 'Fotografi',
                'harga' => 30000,
                'stok' => 3,
                'status_verifikasi' => 'pending',
            ],
            [
                'judul' => 'Kolase Kota Impian',
                'deskripsi' => 'Kolase dari potongan majalah yang menggambarkan kota ramah lingkungan.',
                'kategori' => 'Seni Rupa',
                'harga' => 35000,
                'stok' => 1,
                'status_verifikasi' => 'pending',
            ],
            [
                'judul' => 'Melodi untuk Sahabat',
                'deskripsi' => 'Lagu akustik pendek tentang persahabatan dan kebersamaan di sekolah.',
                'kategori' => 'Musik',
                'harga' => 25000,
                'stok' => 5,
                'status_verifikasi' => 'pending',
            ],
            [
                'judul' => 'Cerita Si Kancil Penolong',
                'deskripsi' => 'Cerita pendek tentang kancil yang membantu teman-temannya mengatasi masalah.',
                'kategori' => 'Seni Sastra',
                'harga' => 0,
                'stok' => 10,
                'status_verifikasi' => 'pending',
            ],
            [
                'judul' => 'Tempat Pensil dari Kaleng',
                'deskripsi' => 'Tempat pensil berwarna-warni yang dibuat dari kaleng bekas dan kain perca.',
                'kategori' => 'Karya Fisik',
                'harga' => 20000,
                'stok' => 4,
                'status_verifikasi' => 'pending',
            ],
            [
                'judul' => 'Kalkulator Pecahan Sederhana',
                'deskripsi' => 'Aplikasi kecil untuk membantu siswa berlatih menghitung pecahan.',
                'kategori' => 'Software',
                'harga' => 0,
                'stok' => 10,
                'status_verifikasi' => 'pending',
            ],
            [
                'judul' => 'Ikan Koi dari Kertas Lipat',
                'deskripsi' => 'Hiasan ikan koi berwarna-warni yang dibuat dengan teknik origami.',
                'kategori' => 'Seni Rupa',
                'harga' => 18000,
                'stok' => 6,
                'status_verifikasi' => 'pending',
            ],
            [
                'judul' => 'Pemandangan dari Jendela Kelas',
                'deskripsi' => 'Lukisan pemandangan halaman sekolah yang terlihat dari jendela kelas.',
                'kategori' => 'Lukisan',
                'harga' => 55000,
                'stok' => 1,
                'status_verifikasi' => 'pending',
            ],
            [
                'judul' => 'Kartu Ucapan Buatan Tangan',
                'deskripsi' => 'Kartu ucapan dengan hiasan bunga kering untuk diberikan kepada keluarga.',
                'kategori' => 'Karya Fisik',
                'harga' => 12000,
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

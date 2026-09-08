# Karya Murid

Aplikasi katalog dan penjualan karya murid berbasis Laravel. Pengguna dapat mengelola karya, memasukkan produk ke keranjang, melakukan pembayaran melalui Midtrans Snap, dan melihat ringkasan penjualan pada dashboard.

## Persyaratan

- PHP 8.2 atau lebih baru
- Composer
- Node.js dan npm
- MySQL atau database yang didukung Laravel
- Akun Midtrans Sandbox untuk pengujian pembayaran

## Menjalankan Aplikasi

1. Install dependensi PHP dan JavaScript:

   ```bash
   composer install
   npm install
   ```

2. Siapkan konfigurasi lingkungan:

   ```bash
   copy .env.example .env
   php artisan key:generate
   ```

3. Isi koneksi database pada `.env`, lalu jalankan migrasi:

   ```bash
   php artisan migrate
   ```

4. Konfigurasi kunci Midtrans pada `.env`:

   ```dotenv
   MIDTRANS_SERVER_KEY=...
   MIDTRANS_CLIENT_KEY=...
   MIDTRANS_IS_PRODUCTION=false
   ```

5. Jalankan server Laravel dan Vite pada terminal terpisah:

   ```bash
   php artisan serve
   npm run dev
   ```

## Alur Pembayaran

1. Checkout membuat transaksi berstatus `pending`, menyimpan detail produk, mengosongkan keranjang, lalu meminta Snap Token dari Midtrans.
2. Halaman detail transaksi membuka Midtrans Snap. Setelah pembayaran sukses, browser memanggil endpoint konfirmasi pembayaran.
3. Endpoint konfirmasi meminta status order langsung ke Midtrans. Ini memungkinkan aplikasi lokal memperbarui transaksi meskipun webhook Midtrans tidak dapat mengakses `localhost`.
4. Midtrans juga mengirim notifikasi ke `POST /midtrans/callback`. Webhook dan endpoint konfirmasi menggunakan logika sinkronisasi yang sama.
5. Untuk status `settlement` atau `capture` yang valid, stok dikurangi satu kali dan status transaksi disimpan.

Pemrosesan dibuat idempoten: notifikasi atau konfirmasi yang sama dapat diterima berulang tanpa mengurangi stok dua kali. Pada server yang dapat diakses Midtrans, arahkan URL notifikasi pembayaran Midtrans ke URL publik aplikasi dengan path `/midtrans/callback`.

## Dashboard

Dashboard mengambil data dari `transaksi_details` dan hanya menghitung transaksi berstatus `settlement` atau `capture`:

- **Karya terjual**: total `jumlah` detail transaksi milik karya pengguna.
- **Total pendapatan**: `harga_satuan * jumlah` dari detail transaksi tersebut.
- **Tren penjualan**: pendapatan yang dikelompokkan berdasarkan tanggal detail transaksi dalam tujuh hari terakhir.

## Struktur Fitur Utama

- `app/Http/Controllers/TransaksiController.php`: checkout, detail transaksi, webhook, dan konfirmasi status pembayaran.
- `app/Http/Controllers/DashboardController.php`: ringkasan dan tren penjualan pemilik karya.
- `resources/views/transaksi/show.blade.php`: tombol Midtrans Snap dan konfirmasi pembayaran dari browser.
- `routes/web.php`: route katalog, keranjang, transaksi, callback Midtrans, dan dashboard.
- `database/migrations/`: struktur tabel pengguna, karya, keranjang, transaksi, dan detail transaksi.

## Pengujian

Jalankan test suite dengan:

```bash
php artisan test
```

Pastikan database testing sudah dikonfigurasi dan migrasi dijalankan oleh environment test.
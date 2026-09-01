<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Keranjang;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();
        $keranjangs = Keranjang::with('karya')->where('user_id', $user->id)->get();

        if ($keranjangs->isEmpty()) {
            return redirect()->back()->with('error', 'Keranjang belanja Anda masih kosong.');
        }

        DB::beginTransaction();
        try {
            $gross_amount = 0;
            foreach ($keranjangs as $item) {
                $gross_amount += $item->karya->harga * $item->jumlah;
            }

            // 1. Buat data transaksi utama
            $transaksi = Transaksi::create([
                'order_id' => 'TRX-' . time() . '-' . $user->id,
                'user_id' => $user->id,
                'gross_amount' => $gross_amount,
                'transaction_status' => 'pending', 
            ]);

            // 2. Simpan detail transaksi
            foreach ($keranjangs as $item) {
                TransaksiDetail::create([
                    'transaksi_id' => $transaksi->id,
                    'karya_id' => $item->karya_id,
                    'harga_satuan' => $item->karya->harga,
                    'jumlah' => $item->jumlah
                ]);
            }

            // 3. Bersihkan keranjang
            Keranjang::where('user_id', $user->id)->delete();

            // 4. Konfigurasi Midtrans
            \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            // 5. Susun Parameter Pembayaran
            $params = [
                'transaction_details' => [
                    'order_id' => $transaksi->order_id,
                    'gross_amount' => $transaksi->gross_amount,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                ],
            ];

            // 6. Dapatkan Snap Token dan Simpan ke DB
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $transaksi->update(['snap_token' => $snapToken]);

            DB::commit();

            // Redirect ke halaman detail transaksi untuk memunculkan popup pembayaran
            return redirect()->route('transaksi.show', $transaksi->id)->with('success', 'Berhasil checkout! Silakan selesaikan pembayaran.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses checkout: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $transaksi = Transaksi::with(['details.karya'])->where('user_id', Auth::id())->findOrFail($id);
        
        // Jika status sudah success, mungkin tidak perlu menampilkan tombol bayar lagi
        return view('transaksi.show', compact('transaksi'));
    }

    // Tambahkan method ini untuk Riwayat Transaksi
    public function index()
    {
        // Ambil semua transaksi milik user yang sedang login, urutkan dari yang terbaru
        $transaksis = Transaksi::with(['details.karya'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('transaksi.index', compact('transaksis'));
    }
}
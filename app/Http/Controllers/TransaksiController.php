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
    /**
     * Membuat transaksi pending dari isi keranjang dan meminta Snap Token Midtrans.
     * Detail transaksi disimpan sebelum keranjang dikosongkan agar isi pesanan
     * tetap tersedia untuk webhook maupun konfirmasi status pembayaran.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Eager loading karya mencegah query tambahan saat menghitung total dan menyimpan detail.
        $keranjangs = Keranjang::with('karya')->where('user_id', $user->id)->get();

        if ($keranjangs->isEmpty()) {
            return redirect()->back()->with('error', 'Keranjang belanja Anda masih kosong.');
        }

        DB::beginTransaction();
        try {
            // Total dihitung dari harga saat checkout; detail di bawah menyimpan snapshot yang sama.
            $gross_amount = 0;
            foreach ($keranjangs as $item) {
                $gross_amount += $item->karya->harga * $item->jumlah;
            }

            // Simpan snapshot harga dan jumlah saat checkout; harga katalog dapat berubah setelahnya.
            $transaksi = Transaksi::create([
                'order_id' => 'TRX-' . time() . '-' . $user->id,
                'user_id' => $user->id,
                'gross_amount' => $gross_amount,
                'transaction_status' => 'pending', 
            ]);

            foreach ($keranjangs as $item) {
                TransaksiDetail::create([
                    'transaksi_id' => $transaksi->id,
                    'karya_id' => $item->karya_id,
                    'harga_satuan' => $item->karya->harga,
                    'jumlah' => $item->jumlah
                ]);
            }

            Keranjang::where('user_id', $user->id)->delete();

            $this->configureMidtrans();

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

            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $transaksi->update(['snap_token' => $snapToken]);

            // Jangan expose transaksi sebelum token dan seluruh data lokal berhasil disimpan.
            DB::commit();

            return redirect()->route('transaksi.show', $transaksi->id)->with('success', 'Berhasil checkout! Silakan selesaikan pembayaran.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses checkout: ' . $e->getMessage());
        }
    }

    /** Menampilkan ringkasan transaksi dan tombol pembayaran untuk pemiliknya. */
    public function show($id)
    {
        $transaksi = Transaksi::with(['details.karya'])->where('user_id', Auth::id())->findOrFail($id);
        
        return view('transaksi.show', compact('transaksi'));
    }

    /** Menampilkan riwayat transaksi milik pengguna yang sedang login. */
    public function index()
    {
        $transaksis = Transaksi::with(['details.karya'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('transaksi.index', compact('transaksis'));
    }

    /**
     * Menerima notifikasi server-to-server dari Midtrans.
     * Route ini dikecualikan dari CSRF karena dipanggil oleh Midtrans, bukan browser pengguna.
     */
    public function callback(Request $request)
    {
        // Notification membaca payload dan signature Midtrans dari request masuk.
        $this->configureMidtrans();

        $notification = new \Midtrans\Notification();
        $this->syncPaymentStatus(
            $notification->order_id,
            $notification->transaction_status,
            $notification->payment_type,
            $notification->fraud_status ?? null
        );

        return response()->json(['status' => 'ok']);
    }

    /**
     * Mengambil status order langsung dari Midtrans setelah Snap sukses di browser.
     * Jalur ini penting saat aplikasi masih lokal dan webhook tidak dapat menjangkaunya.
     */
    public function confirmPayment($id)
    {
        $transaksi = Transaksi::where('user_id', Auth::id())->findOrFail($id);

        $this->configureMidtrans();
        $status = \Midtrans\Transaction::status($transaksi->order_id);
        $statusOrderId = data_get($status, 'order_id');
        $transactionStatus = data_get($status, 'transaction_status');
        $paymentType = data_get($status, 'payment_type');
        $fraudStatus = data_get($status, 'fraud_status');

        if ($statusOrderId !== $transaksi->order_id) {
            abort(422, 'Order pembayaran tidak cocok.');
        }

        $this->syncPaymentStatus(
            $statusOrderId,
            $transactionStatus,
            $paymentType,
            $fraudStatus
        );

        return response()->json([
            'status' => $transactionStatus,
            'successful' => in_array($transactionStatus, ['settlement', 'capture'], true),
        ]);
    }

    private function configureMidtrans(): void
    {
        // Konfigurasi harus di-set pada setiap request karena PHP tidak menyimpan state antar-request.
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
    }

    private function syncPaymentStatus(string $orderId, string $transactionStatus, ?string $paymentType, ?string $fraudStatus): void
    {
        // Hanya status settlement/capture yang dianggap berhasil dan boleh mengubah stok.
        $isSuccessful = in_array($transactionStatus, ['settlement', 'capture'], true)
            && ($transactionStatus !== 'capture' || $fraudStatus === null || $fraudStatus === 'accept');

        DB::transaction(function () use ($orderId, $transactionStatus, $paymentType, $isSuccessful) {
            $transaksi = Transaksi::where('order_id', $orderId)->lockForUpdate()->firstOrFail();
            $wasSuccessful = in_array($transaksi->transaction_status, ['settlement', 'capture'], true);

            // Status tetap diperbarui untuk mencatat perubahan seperti pending, deny, atau expire.
            $transaksi->update([
                'transaction_status' => $transactionStatus,
                'payment_type' => $paymentType,
            ]);

            // Lock transaksi dan produk membuat webhook yang datang bersamaan tetap idempoten.
            if ($isSuccessful && !$wasSuccessful) {
                foreach ($transaksi->details as $detail) {
                    $karya = $detail->karya()->lockForUpdate()->firstOrFail();
                    $karya->decrement('stok', $detail->jumlah);
                }
            }
        });
    }
}
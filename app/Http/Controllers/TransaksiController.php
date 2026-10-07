<?php
namespace App\Http\Controllers;

use App\Models\Karya;
use App\Models\Keranjang;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Http\Request;
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

        $keranjangs = Keranjang::where('user_id', $user->id)->get();

        if ($keranjangs->isEmpty()) {
            return redirect()->back()->with('error', 'Keranjang belanja Anda masih kosong.');
        }

        DB::beginTransaction();
        try {
            // Lock products before cart rows, matching the add-to-cart lock order.
            $karyas = Karya::whereIn('id', $keranjangs->pluck('karya_id')->unique())
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');
            $keranjangs = Keranjang::where('user_id', $user->id)->lockForUpdate()->get();

            foreach ($keranjangs->groupBy('karya_id') as $karyaId => $items) {
                $karya = $karyas->get($karyaId);
                $jumlahDiKeranjang = $items->sum('jumlah');

                if (!$karya || $jumlahDiKeranjang > $karya->stok) {
                    $judul = $karya?->judul ?? 'Karya';
                    $stok = $karya?->stok ?? 0;
                    throw new \RuntimeException(
                        "Stok {$judul} tidak mencukupi. Stok tersedia: {$stok}, jumlah di keranjang: {$jumlahDiKeranjang}."
                    );
                }
            }

            // Total dihitung dari harga saat checkout; detail menyimpan snapshot yang sama.
            $gross_amount = 0;
            foreach ($keranjangs as $item) {
                $gross_amount += $karyas[$item->karya_id]->harga * $item->jumlah;
            }

            // Simpan snapshot harga dan jumlah saat checkout; harga katalog dapat berubah setelahnya.
            $transaksi = Transaksi::create([
                'order_id' => 'TRX-' . time() . '-' . $user->id,
                'user_id' => $user->id,
                'gross_amount' => $gross_amount,
                'transaction_status' => 'pending',
            ]);

            foreach ($keranjangs->groupBy('karya_id') as $karyaId => $items) {
                TransaksiDetail::create([
                    'transaksi_id' => $transaksi->id,
                    'karya_id' => $karyaId,
                    'harga_satuan' => $karyas[$karyaId]->harga,
                    'jumlah' => $items->sum('jumlah'),
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

            return redirect()->route('transaksi.show', $transaksi)->with('success', 'Berhasil checkout! Silakan selesaikan pembayaran.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses checkout: ' . $e->getMessage());
        }
    }

    /** Menampilkan ringkasan transaksi dan tombol pembayaran untuk pemiliknya. */
    public function show(Transaksi $transaksi)
    {
        // Model binding + auth check memastikan hanya pemilik transaksi yang dapat melihatnya.
        if ($transaksi->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        $this->refreshPendingPaymentStatus($transaksi);
        $transaksi->load(['details.karya']);
        $hasPaidDuplicateKarya = $this->hasPaidTransactionForSameKarya($transaksi);

        return view('transaksi.show', compact('transaksi', 'hasPaidDuplicateKarya'));
    }

    /** Menampilkan riwayat transaksi milik pengguna yang sedang login. */
    public function index()
    {
       $user = auth()->user();
        $perPage = 10;

        // 1. Pembelian Milik User Sendiri
        $pembelian = Transaksi::with(['details.karya'])
                        ->where('user_id', $user->id)
                        ->latest()
                        ->paginate($perPage, ['*'], 'pembelian_page')
                        ->withQueryString();

        foreach ($pembelian->getCollection() as $transaksi) {
            $this->refreshPendingPaymentStatus($transaksi);
        }

        // 2. Penjualan Karya Milik User Sendiri (untuk Pembeli & Admin)
        $penjualan = collect();
        if (in_array($user->role, ['pembeli', 'admin'])) {
            $penjualan = TransaksiDetail::with(['transaksi.user', 'karya'])
                            ->whereHas('karya', function($query) use ($user) {
                                $query->where('user_id', $user->id);
                            })
                            ->latest()
                            ->paginate($perPage, ['*'], 'penjualan_page')
                            ->withQueryString();
        }

        // 3. Penjualan Global (Khusus Admin)
        $penjualanGlobal = collect();
        if ($user->role === 'admin') {
            $penjualanGlobal = TransaksiDetail::with(['transaksi.user', 'karya.pembuat'])
                                ->latest()
                                ->paginate($perPage, ['*'], 'penjualan_global_page')
                                ->withQueryString();
        }

        return view('transaksi.index', compact('pembelian', 'penjualan', 'penjualanGlobal'));
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
    public function confirmPayment(Transaksi $transaksi)
    {
        // Model binding + auth check memastikan hanya pemilik transaksi yang dapat mengkonfirmasi.
        if ($transaksi->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if ($this->hasPaidTransactionForSameKarya($transaksi)) {
            return response()->json([
                'message' => 'Karya pada transaksi ini sudah dibayar melalui transaksi lain.',
            ], 409);
        }

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

    private function hasPaidTransactionForSameKarya(Transaksi $transaksi): bool
    {
        $karyaIds = $transaksi->details->pluck('karya_id');

        if ($karyaIds->isEmpty()) {
            return false;
        }

        return TransaksiDetail::whereIn('karya_id', $karyaIds)
            ->where('transaksi_id', '!=', $transaksi->id)
            ->whereHas('transaksi', function ($query) use ($transaksi) {
                $query->where('user_id', $transaksi->user_id)
                    ->whereIn('transaction_status', ['settlement', 'capture']);
            })
            ->exists();
    }

    private function configureMidtrans(): void
    {
        // Konfigurasi harus di-set pada setiap request karena PHP tidak menyimpan state antar-request.
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);
        \Midtrans\Config::$isSanitized = config('services.midtrans.is_sanitized', true);
        \Midtrans\Config::$is3ds = config('services.midtrans.is_3ds', true);
    }

    private function refreshPendingPaymentStatus(Transaksi $transaksi): void
    {
        if ($transaksi->transaction_status !== 'pending') {
            return;
        }

        try {
            $this->configureMidtrans();
            $status = \Midtrans\Transaction::status($transaksi->order_id);
            $statusOrderId = data_get($status, 'order_id');
            $transactionStatus = data_get($status, 'transaction_status');

            if ($statusOrderId !== $transaksi->order_id || !is_string($transactionStatus)) {
                return;
            }

            $this->syncPaymentStatus(
                $statusOrderId,
                $transactionStatus,
                data_get($status, 'payment_type'),
                data_get($status, 'fraud_status')
            );
            $transaksi->refresh();
        } catch (\Throwable $e) {
            // Gunakan batas waktu lokal bila status Midtrans tidak dapat diambil.
            $expiryHours = (int) env('MIDTRANS_EXPIRY_HOURS', 24);
            if ($transaksi->created_at?->lte(now()->subHours($expiryHours))) {
                $this->syncPaymentStatus($transaksi->order_id, 'expire', null, null);
                $transaksi->refresh();
            }
        }
    }

    private function syncPaymentStatus(string $orderId, string $transactionStatus, ?string $paymentType, ?string $fraudStatus): void
    {
        $transactionStatus = strtolower(trim($transactionStatus));

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
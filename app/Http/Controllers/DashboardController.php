<?php

namespace App\Http\Controllers;

use App\Models\Karya;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menyediakan kartu ringkasan dan tren tujuh hari untuk karya milik pengguna.
     * Hanya transaksi settlement/capture yang sudah berhasil masuk perhitungan.
     */
    public function index()
    {
        $user = Auth::user();

        // Semua metrik dibatasi pada karya milik pengguna yang sedang login.
        $totalKarya = Karya::where('user_id', $user->id)->count();

        // Detail hanya dihitung setelah pembayaran berhasil agar checkout pending
        // atau pembayaran gagal tidak dianggap sebagai penjualan.
        $totalTerjual = TransaksiDetail::whereHas('karya', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->whereHas('transaksi', function ($q) {
            $q->whereIn('transaction_status', ['settlement', 'capture']);
        })->sum('jumlah');

        $totalPendapatan = TransaksiDetail::whereHas('karya', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->whereHas('transaksi', function ($q) {
            $q->whereIn('transaction_status', ['settlement', 'capture']);
        })->sum(DB::raw('harga_satuan * jumlah'));

        // Grafik memakai tanggal detail transaksi, bukan tanggal pembayaran,
        // sehingga setiap baris produk masuk ke hari penjualan yang tepat.
        $penjualanHarian = TransaksiDetail::select(
            DB::raw('DATE(transaksi_details.created_at) as tanggal'),
            DB::raw('SUM(transaksi_details.jumlah) as total_item'),
            DB::raw('SUM(transaksi_details.harga_satuan * transaksi_details.jumlah) as pendapatan')
        )
            ->join('karyas', 'transaksi_details.karya_id', '=', 'karyas.id')
            ->join('transaksis', 'transaksi_details.transaksi_id', '=', 'transaksis.id')
            ->where('karyas.user_id', $user->id)
            ->where(function ($q) {
                $q->whereIn('transaksis.transaction_status', ['settlement', 'capture']);
            })
            ->where('transaksi_details.created_at', '>=', now()->subDays(7))
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        // Chart.js membutuhkan dua array terpisah: label tanggal dan nilai pendapatan.
        $chartLabels = $penjualanHarian->pluck('tanggal')->toArray();
        $chartData = $penjualanHarian->pluck('pendapatan')->toArray();

        return view('dashboard.index', compact(
            'totalKarya',
            'totalTerjual',
            'totalPendapatan',
            'chartLabels',
            'chartData'
        ));

    }
}
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
    public function index()
    {
        $user = Auth::user();

        // 1. Kartu Ringkasan (Summary Cards)
        $totalKarya = Karya::where('user_id', $user->id)->count();
        $totalTerjual = TransaksiDetail::whereHas('karya', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->whereHas('transaksi', function ($q) {
            $q->where('transaction_status', 'settlement') // Hanya transaksi sukses (dari Midtrans)
                ->orWhere('transaction_status', 'capture');
        })->sum('jumlah');

        $totalPendapatan = TransaksiDetail::whereHas('karya', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->whereHas('transaksi', function ($q) {
            $q->where('transaction_status', 'settlement')
                ->orWhere('transaction_status', 'capture');
        })->sum(\DB::raw('harga_satuan * jumlah')); // Pastikan subtotal dihitung: harga_satuan * jumlah

        // 2. Data Grafik Penjualan (7 Hari Terakhir)
        $penjualanHarian = TransaksiDetail::select(
            DB::raw('DATE(transaksi_details.created_at) as tanggal'),
            DB::raw('SUM(transaksi_details.jumlah) as total_item'),
            DB::raw('SUM(transaksi_details.harga_satuan * transaksi_details.jumlah) as pendapatan')
        )
            ->join('karyas', 'transaksi_details.karya_id', '=', 'karyas.id')
            ->join('transaksis', 'transaksi_details.transaksi_id', '=', 'transaksis.id')
            ->where('karyas.user_id', $user->id)
            ->where(function ($q) {
                $q->where('transaksis.transaction_status', 'settlement')
                    ->orWhere('transaksis.transaction_status', 'capture');
            })
            ->where('transaksi_details.created_at', '>=', now()->subDays(7))
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        // Format data untuk Chart.js (Label: Tanggal, Data: Pendapatan)
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
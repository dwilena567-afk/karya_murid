<?php

namespace App\Http\Controllers;

use App\Models\Karya;
use App\Models\TransaksiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menyediakan ringkasan dan tren penjualan untuk pengguna yang sedang login.
     * Hanya transaksi settlement/capture yang sudah berhasil masuk perhitungan.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'periode' => ['sometimes', 'in:7_minggu,1_bulan,1_tahun,all_time'],
            'periode_populer' => ['sometimes', 'in:7_minggu,1_bulan,1_tahun,all_time'],
        ]);
        $periode = $validated['periode'] ?? '7_minggu';
        $periodePopuler = $validated['periode_populer'] ?? '7_minggu';
        $akhirPeriode = now();
        $durasi = [
            '7_minggu' => ['label' => '7 Minggu Terakhir', 'mulai' => $akhirPeriode->copy()->subWeeks(7)],
            '1_bulan' => ['label' => '1 Bulan Terakhir', 'mulai' => $akhirPeriode->copy()->subMonth()],
            '1_tahun' => ['label' => '1 Tahun Terakhir', 'mulai' => $akhirPeriode->copy()->subYear()],
            'all_time' => ['label' => 'Sepanjang Waktu', 'mulai' => null],
        ];
        $durasiTren = $durasi[$periode] + ['sampai' => $akhirPeriode];
        $durasiPopuler = $durasi[$periodePopuler] + ['sampai' => $akhirPeriode];

        $user = Auth::user();
        $karyasMenungguVerifikasi = collect();

        if ($user->role === 'admin') {
            $karyasMenungguVerifikasi = Karya::with(['pembuat', 'kategori'])
                ->where('status_verifikasi', 'pending')
                ->latest()
                ->paginate(5, ['*'], 'verifikasi_page')
                ->withQueryString()
                ->fragment('verifikasi');
        }

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
            ->when($durasiTren['mulai'], function ($query, $mulai) {
                $query->where('transaksi_details.created_at', '>=', $mulai);
            })
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        // Chart.js membutuhkan dua array terpisah: label tanggal dan nilai pendapatan.
        $chartLabels = $penjualanHarian->pluck('tanggal')->toArray();
        $chartData = $penjualanHarian->pluck('pendapatan')->toArray();

        $karyaTerpopuler = TransaksiDetail::select(
            'karyas.id',
            'karyas.judul',
            'karyas.gambar',
            DB::raw('SUM(transaksi_details.jumlah) as total_terjual')
        )
            ->join('karyas', 'transaksi_details.karya_id', '=', 'karyas.id')
            ->join('transaksis', 'transaksi_details.transaksi_id', '=', 'transaksis.id')
            ->where('karyas.user_id', $user->id)
            ->whereIn('transaksis.transaction_status', ['settlement', 'capture'])
            ->when($durasiPopuler['mulai'], function ($query, $mulai) {
                $query->where('transaksi_details.created_at', '>=', $mulai);
            })
            ->groupBy('karyas.id', 'karyas.judul', 'karyas.gambar')
            ->orderByDesc('total_terjual')
            ->orderBy('karyas.judul')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'totalKarya',
            'totalTerjual',
            'totalPendapatan',
            'chartLabels',
            'chartData',
            'karyaTerpopuler',
            'karyasMenungguVerifikasi',
            'periode',
            'periodePopuler',
            'durasiTren',
            'durasiPopuler'
        ));
        

    }
}
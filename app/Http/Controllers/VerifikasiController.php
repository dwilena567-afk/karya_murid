<?php

namespace App\Http\Controllers;

use App\Models\Karya;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VerifikasiController extends Controller
{
    /**
     * Menampilkan daftar karya yang masih berstatus 'pending'.
     */
    public function index()
    {
        return redirect()->to(route('dashboard.index') . '#verifikasi');
    }

    /**
     * Menyetujui karya (Ubah status jadi 'approved').
     */
    public function approve(Karya $karya)
    {
        // Status approved membuat karya tampil di katalog dan beranda publik.
        $karya->update([
            'status_verifikasi' => 'approved'
        ]);

        return redirect()->to(route('dashboard.index') . '#verifikasi')
            ->with('success', 'Karya "' . $karya->judul . '" berhasil disetujui dan kini tampil di katalog.');
    }

    /**
     * Menolak karya (Ubah status jadi 'rejected').
     */
    public function reject(Karya $karya)
    {
        // Status rejected menyimpan keputusan admin tanpa menghapus data karya.
        $karya->update([
            'status_verifikasi' => 'rejected'
        ]);

        return redirect()->to(route('dashboard.index') . '#verifikasi')
            ->with('success', 'Karya "' . $karya->judul . '" telah ditolak.');
    }

    public function hide(Karya $karya)
    {
        $karya->update([
            'status_verifikasi' => 'rejected',
        ]);

        return redirect()->route('katalog.index')
            ->with('success', 'Karya "' . $karya->judul . '" disembunyikan dari katalog.');
    }

    public function destroy(Karya $karya)
    {
        $karya->load('gambarTambahan');
        $gambarPaths = $karya->semua_gambar->all();
        $judul = $karya->judul;

        DB::transaction(function () use ($karya) {
            $pendingTransactionIds = TransaksiDetail::where('karya_id', $karya->id)
                ->whereHas('transaksi', fn ($query) => $query->where('transaction_status', 'pending'))
                ->pluck('transaksi_id')
                ->unique();

            Transaksi::whereIn('id', $pendingTransactionIds)
                ->where('transaction_status', 'pending')
                ->update([
                    'transaction_status' => 'cancel',
                    'payment_type' => 'item_deleted',
                ]);

            $karya->delete();
        });

        if ($gambarPaths) {
            Storage::disk('public')->delete($gambarPaths);
        }

        return redirect()->route('katalog.index')
            ->with('success', 'Karya "' . $judul . '" berhasil dihapus secara permanen.');
    }
}
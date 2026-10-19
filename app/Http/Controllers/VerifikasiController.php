<?php

namespace App\Http\Controllers;

use App\Models\Karya;
use Illuminate\Http\Request;

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
}
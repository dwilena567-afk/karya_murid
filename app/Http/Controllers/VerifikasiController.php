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
        // Admin hanya perlu melihat karya yang menunggu keputusan, dengan item terbaru di atas.
        $karyas = Karya::with(['pembuat', 'kategori'])
            ->where('status_verifikasi', 'pending')
            ->latest()
            ->get();

        return view('verifikasi.index', compact('karyas'));
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

        return redirect()->route('verifikasi.index')
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

        return redirect()->route('verifikasi.index')
            ->with('success', 'Karya "' . $karya->judul . '" telah ditolak.');
    }
}
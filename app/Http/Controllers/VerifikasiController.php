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
        // Mengambil karya yang statusnya pending, urut dari yang paling baru
        $karyas = Karya::with(['pembuat', 'kategori'])
            ->where('status_verifikasi', 'pending')
            ->latest()
            ->get();

        return view('verifikasi.index', compact('karyas'));
    }

    /**
     * Menyetujui karya (Ubah status jadi 'approved').
     */
    public function approve($id)
    {
        $karya = Karya::findOrFail($id);
        
        $karya->update([
            'status_verifikasi' => 'approved'
        ]);

        return redirect()->route('verifikasi.index')
            ->with('success', 'Karya "' . $karya->judul . '" berhasil disetujui dan kini tampil di katalog.');
    }

    /**
     * Menolak karya (Ubah status jadi 'rejected').
     */
    public function reject($id)
    {
        $karya = Karya::findOrFail($id);
        
        $karya->update([
            'status_verifikasi' => 'rejected'
        ]);

        return redirect()->route('verifikasi.index')
            ->with('success', 'Karya "' . $karya->judul . '" telah ditolak.');
    }
}
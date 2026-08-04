<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karya;

class VerifikasiController extends Controller
{
    // 1. Menampilkan daftar karya yang berstatus 'pending'
    public function index()
    {
        $karyas = Karya::where('status_verifikasi', 'pending')->with('pembuat')->get();
        return view('verifikasi.index', compact('karyas'));
    }

    // 2. Fungsi untuk menyetujui (Approve) karya
    public function approve($id)
    {
        $karya = Karya::findOrFail($id);
        $karya->update(['status_verifikasi' => 'approved']);
        
        return redirect()->back()->with('success', 'Karya berhasil disetujui dan masuk ke Katalog!');
    }

    // 3. Fungsi untuk menolak (Reject) karya
    public function reject($id)
    {
        $karya = Karya::findOrFail($id);
        $karya->update(['status_verifikasi' => 'rejected']);
        
        return redirect()->back()->with('error', 'Karya ditolak.');
    }
}

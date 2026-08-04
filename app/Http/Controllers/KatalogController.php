<?php

namespace App\Http\Controllers;
use App\Models\Karya;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    public function index()
    {
        // Mengambil semua karya yang statusnya 'approved' (disetujui)
        $karyas = Karya::where('status_verifikasi', 'approved')->get();
        
        // Mengirim data karya ke halaman view katalog
        return view('katalog.index', compact('karyas'));
    }

    public function show($id)
    {
        // Mengambil detail karya beserta nama pembuatnya
        $karya = Karya::with('pembuat')->findOrFail($id);
        
        // Memastikan hanya karya yang 'approved' yang bisa dilihat detailnya
        if ($karya->status_verifikasi !== 'approved') {
            abort(404, 'Karya tidak ditemukan atau belum disetujui.');
        }

        return view('katalog.show', compact('karya'));
    }
}

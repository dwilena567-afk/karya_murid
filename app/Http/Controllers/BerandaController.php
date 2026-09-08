<?php

namespace App\Http\Controllers;
use App\Models\Karya;
use Illuminate\Http\Request;

class BerandaController extends Controller
{
    public function index()
    {
        // Beranda hanya menampilkan karya approved dan membatasi jumlah agar halaman tetap ringkas.
        $karyasTerbaru = Karya::with('pembuat')
            ->where('status_verifikasi', 'approved')
            ->latest()
            ->take(4)
            ->get();

        return view('beranda.index', compact('karyasTerbaru'));
    }
}

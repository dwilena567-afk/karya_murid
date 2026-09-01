<?php

namespace App\Http\Controllers;
use App\Models\Karya;
use Illuminate\Http\Request;

class BerandaController extends Controller
{
    public function index()
    {
        // Ambil 4 karya yang terverifikasi
        $karyasTerbaru = Karya::with('pembuat')
            ->where('status_verifikasi', 'approved')
            ->latest()
            ->take(4)
            ->get();

        return view('beranda.index', compact('karyasTerbaru'));
    }
}

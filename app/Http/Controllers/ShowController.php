<?php

namespace App\Http\Controllers;
use App\Models\Karya;
use Illuminate\Http\Request;

class ShowController extends Controller
{
      public function index()
    {
        // menampilkan karya lainnya di bawah detail karya katalog
        $karyasdetails = Karya::with(['pembuat', 'kategori'])
            ->where('status_verifikasi', 'approved')
            ->latest()
            ->take(4)
            ->get();

        return view('katalog.show', compact('karyasdetails'));
    }
}

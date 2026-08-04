<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function store(Request $request)
    {
        // Nanti logika memindahkan data dari Keranjang ke Transaksi
        // dan memanggil Midtrans akan kita tulis di sini.
        
        return redirect()->back()->with('info', 'Fitur checkout sedang dibangun!');
    }
}

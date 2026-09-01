<?php

namespace App\Http\Controllers;
use App\Models\Keranjang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class KeranjangController extends Controller
{
    public function index()
    {
        
        $keranjangs = Keranjang::where('user_id', Auth::id())->with('karya')->get();
        return view('keranjang.index', compact('keranjangs'));
    }

    public function store(Request $request)
    {
       
        $request->validate([
            'karya_id' => 'required|exists:karyas,id',
            'jumlah' => 'required|integer|min:1'
        ]);

        
        Keranjang::create([
            'user_id' => Auth::id(),
            'karya_id' => $request->karya_id,
            'jumlah' => $request->jumlah
        ]);

        return redirect()->back()->with('success', 'Karya berhasil ditambahkan ke keranjang!');
    }

    public function destroy($id)
    {
        $keranjang = Keranjang::where('user_id', Auth::id())->findOrFail($id);
        $keranjang->delete();

        return redirect()->back()->with('success', 'Karya berhasil dihapus dari keranjang.');
    }
}

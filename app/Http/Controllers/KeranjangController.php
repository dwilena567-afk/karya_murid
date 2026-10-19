<?php

namespace App\Http\Controllers;
use App\Models\Karya;
use App\Models\Keranjang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KeranjangController extends Controller
{
    public function index()
    {
        // Hanya tampilkan isi keranjang pengguna aktif dan muat data karya untuk harga/gambar.
        $keranjangs = Keranjang::where('user_id', Auth::id())->with('karya')->get();
        return view('keranjang.index', compact('keranjangs'));
    }

    public function store(Request $request)
    {
        // Validasi mencegah produk fiktif dan jumlah nol masuk ke keranjang.
        $validated = $request->validate([
            'karya_id' => 'required|exists:karyas,id',
            'jumlah' => 'required|integer|min:1'
        ]);

        // Lock the work so simultaneous add requests cannot exceed its stock.
        $error = DB::transaction(function () use ($validated) {
            $karya = Karya::whereKey($validated['karya_id'])->lockForUpdate()->firstOrFail();
            $items = Keranjang::where('user_id', Auth::id())
                ->where('karya_id', $karya->id)
                ->lockForUpdate()
                ->get();
            $jumlahDiKeranjang = $items->sum('jumlah');
            $jumlahBaru = $jumlahDiKeranjang + $validated['jumlah'];

            if ($jumlahBaru > $karya->stok) {
                return "Stok {$karya->judul} tidak mencukupi. Stok tersedia: {$karya->stok}, jumlah di keranjang: {$jumlahDiKeranjang}.";
            }

            if ($items->isEmpty()) {
                Keranjang::create([
                    'user_id' => Auth::id(),
                    'karya_id' => $karya->id,
                    'jumlah' => $jumlahBaru,
                ]);
            } else {
                $item = $items->first();
                $item->update(['jumlah' => $jumlahBaru]);
                $items->skip(1)->each->delete();
            }

            return null;
        });

        if ($error !== null) {
            return redirect()->back()->with('error', $error);
        }

        return redirect()->back()->with('success', 'Karya berhasil ditambahkan ke keranjang!');
    }

    public function destroy(Keranjang $keranjang)
    {
        // Pembatasan user_id memastikan pengguna hanya dapat menghapus item keranjangnya sendiri.
        if ($keranjang->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        $keranjang->delete();

        return redirect()->back()->with('success', 'Karya berhasil dihapus dari keranjang.');
    }
}

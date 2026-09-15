<?php

namespace App\Http\Controllers;
use App\Models\Karya;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        // Karya pending/rejected tidak boleh muncul di katalog publik.
        $query = Karya::with(['pembuat', 'kategori'])->where('status_verifikasi', 'approved');

        // Filter diterapkan bertahap agar kombinasi pencarian, kategori, dan harga dapat digunakan.
        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        if ($request->filled('min_harga')) {
            $query->where('harga', '>=', $request->min_harga);
        }
        if ($request->filled('max_harga')) {
            $query->where('harga', '<=', $request->max_harga);
        }

        // Urutan default terbaru; pilihan pengguna hanya mengubah kolom harga.
        if ($request->filled('sort')) {
            if ($request->sort == 'termurah') {
                $query->orderBy('harga', 'asc');
            } elseif ($request->sort == 'termahal') {
                $query->orderBy('harga', 'desc');
            } else {
                // Default ke terbaru
                $query->orderBy('created_at', 'desc');
            }
        } else {
            // Urutan bawaan jika user tidak memilih filter urutan
            $query->orderBy('created_at', 'desc');
        }

        //$karyas = $query->get();
        $karyas = $query->paginate(8)->withQueryString();

        return view('katalog.index', compact('karyas'));
    }

   

    public function show(Karya $karya)
    {
        // Relasi kategori diperlukan untuk detail tampilan; model binding menangani 404 otomatis.
        $karya->load('kategori');

        // Validasi ulang status mencegah karya yang belum disetujui diakses melalui URL langsung.
        if ($karya->status_verifikasi !== 'approved') {
            abort(404, 'Karya tidak ditemukan atau belum disetujui.');
        }

        return view('katalog.show', compact('karya'));
    }
}

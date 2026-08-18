<?php

namespace App\Http\Controllers;
use App\Models\Karya;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        // 1. Mulai query, hanya ambil karya yang sudah disetujui (approved)
        $query = Karya::with(['pembuat', 'kategori'])->where('status_verifikasi', 'approved');

        // 2. Filter Pencarian Judul
        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        // 3. Filter Kategori
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        // 4. Filter Rentang Harga (Min dan Max)
        if ($request->filled('min_harga')) {
            $query->where('harga', '>=', $request->min_harga);
        }
        if ($request->filled('max_harga')) {
            $query->where('harga', '<=', $request->max_harga);
        }

        // 5. Logika Pengurutan (Sort)
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

        // 6. Eksekusi query dan ambil datanya
        $karyas = $query->get();

        // 7. Kirim data ke tampilan (view) blade
        return view('katalog.index', compact('karyas'));
    }

    public function show($id)
    {
        
        $karya = Karya::with('pembuat')->findOrFail($id);
        
       
        if ($karya->status_verifikasi !== 'approved') {
            abort(404, 'Karya tidak ditemukan atau belum disetujui.');
        }

        return view('katalog.show', compact('karya'));
    }
}

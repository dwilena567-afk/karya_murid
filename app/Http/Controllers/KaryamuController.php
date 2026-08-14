<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karya;
use App\Models\Kategori;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class KaryamuController extends Controller
{
    public function index(Request $request)
    {
        
        $query = Karya::with('kategori')->where('user_id', Auth::id());

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

        if ($request->filled('sort')){
            if ($request->sort == 'termurah'){
                $query->orderBy('harga', 'asc');
            } elseif ($request->sort == 'termahal'){
                $query->orderBy('harga', 'desc');
            } else {
                $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $karyas = $query->get();
        return view('karyamu.index', compact('karyas'));

    }

    public function create()
    {
        $kategoris = Kategori::all();
        return view('karyamu.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kategori_id' => 'required|exists:kategoris,id',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:1',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $gambarPath = $request->file('gambar')->store('karya_images', 'public');

        Karya::create([
            'user_id' => Auth::id(),
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'kategori_id' => $request->kategori_id,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'gambar' => $gambarPath,
            'status_verifikasi' => 'pending',
            
        ]);

        return redirect()->route('karyamu.index')->with('success', 'Karya berhasil diunggah dan menunggu verifikasi.');
    }

    public function edit($id)
    {
        $karya = Karya::where('user_id', Auth::id())->findOrFail($id);
        $kategoris = Kategori::all();
        return view('karyamu.edit', compact('karya', 'kategoris'));
    }

    public function update(Request $request, $id)
    {
        $karya = Karya::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kategori_id' => 'required|exists:kategoris,id',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:1',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'kategori_id' => $request->kategori_id,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'status_verifikasi' => 'pending', 
        ];

        if ($request->hasFile('gambar')) {
            
            if ($karya->gambar) {
                Storage::disk('public')->delete($karya->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('karya_images', 'public');
        }

        $karya->update($data);

        return redirect()->route('karyamu.index')->with('success', 'Karya berhasil diperbarui');

       
    }

    public function destroy($id)
    {
        $karya = Karya::where('user_id', Auth::id())->findOrFail($id);

        // Hapus gambar dari storage jika ada
        if ($karya->gambar) {
            Storage::disk('public')->delete($karya->gambar);
        }

        $karya->delete();

        return redirect()->route('karyamu.index')->with('success', 'Karya berhasil dihapus');
    }
}

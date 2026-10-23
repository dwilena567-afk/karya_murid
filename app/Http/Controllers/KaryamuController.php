<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karya;
use App\Models\Kategori;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;


class KaryamuController extends Controller
{
    public function index(Request $request)
    {
        // Daftar karya selalu dibatasi ke pemilik yang sedang login.
        $query = Karya::with('kategori')->where('user_id', Auth::id());

        // Filter opsional dapat digabungkan untuk memudahkan pemilik mencari karya tertentu.
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

        // Terbaru menjadi urutan default agar perubahan terakhir terlihat lebih dahulu.
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

        //$karyas = $query->get();
        $karyas = $query->paginate(8)->withQueryString();

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
            'judul' => 'required|string|max:50',
            'deskripsi' => 'required|string',
            'kategori_id' => 'required|exists:kategoris,id',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:1',
            'gambar' => 'required|array|min:1|max:5',
            'gambar.*' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'gambar.max' => 'Maksimal 5 gambar untuk setiap karya.',
            'gambar.*.max' => 'Ukuran setiap gambar maksimal 2 MB.',
        ]);

        $gambarPaths = collect($request->file('gambar'))
            ->map(fn ($gambar) => $gambar->store('karya_images', 'public'));

        try {
            // Karya baru wajib diverifikasi admin sebelum tampil di katalog publik.
            DB::transaction(function () use ($request, $gambarPaths) {
                $karya = Karya::create([
                    'user_id' => Auth::id(),
                    'judul' => $request->judul,
                    'deskripsi' => $request->deskripsi,
                    'kategori_id' => $request->kategori_id,
                    'harga' => $request->harga,
                    'stok' => $request->stok,
                    'gambar' => $gambarPaths->first(),
                    'status_verifikasi' => 'pending',
                ]);

                foreach ($gambarPaths->skip(1) as $gambarPath) {
                    $karya->gambarTambahan()->create(['gambar' => $gambarPath]);
                }
            });
        } catch (Throwable $exception) {
            if (!Storage::disk('public')->delete($gambarPaths->all())) {
                Log::error('Gagal membersihkan gambar karya setelah penyimpanan karya gagal.', [
                    'gambar' => $gambarPaths->all(),
                ]);
            }

            throw $exception;
        }

        return redirect()->route('karyamu.index')->with('success', 'Karya berhasil diunggah dan menunggu verifikasi.');
    }

    public function edit($id)
    {
        // findOrFail setelah filter user_id mencegah pengeditan karya milik pengguna lain.
        $karya = Karya::with('gambarTambahan')->where('user_id', Auth::id())->findOrFail($id);
        $kategoris = Kategori::all();
        return view('karyamu.edit', compact('karya', 'kategoris'));
    }

    public function update(Request $request, $id)
    {
        // Batas kepemilikan diterapkan lagi pada update, bukan hanya pada halaman edit.
        $karya = Karya::with('gambarTambahan')->where('user_id', Auth::id())->findOrFail($id);
        $slotGambarTersedia = 5 - $karya->semua_gambar->count();

        $request->validate([
            'judul' => 'required|string|max:30',
            'deskripsi' => 'required|string',
            'kategori_id' => 'required|exists:kategoris,id',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:1',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_tambahan' => ['nullable', 'array', 'max:' . $slotGambarTersedia],
            'gambar_tambahan.*' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'gambar_tambahan.max' => 'Jumlah gambar untuk satu karya maksimal 5.',
            'gambar_tambahan.*.max' => 'Ukuran setiap gambar maksimal 2 MB.',
        ]);

        $data = [
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'kategori_id' => $request->kategori_id,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'status_verifikasi' => 'pending', 
        ];

        $gambarPathLama = $karya->gambar;
        $gambarPathBaru = null;
        $gambarTambahanBaru = collect();

        if ($request->hasFile('gambar')) {
            $gambarPathBaru = $request->file('gambar')->store('karya_images', 'public');
            $data['gambar'] = $gambarPathBaru;
        }

        if ($request->hasFile('gambar_tambahan')) {
            $gambarTambahanBaru = collect($request->file('gambar_tambahan'))
                ->map(fn ($gambar) => $gambar->store('karya_images', 'public'));
        }

        try {
            DB::transaction(function () use ($karya, $data, $gambarTambahanBaru) {
                $karya->update($data);

                foreach ($gambarTambahanBaru as $gambarPath) {
                    $karya->gambarTambahan()->create(['gambar' => $gambarPath]);
                }
            });
        } catch (Throwable $exception) {
            $pathsToClean = $gambarTambahanBaru->all();
            if ($gambarPathBaru) {
                $pathsToClean[] = $gambarPathBaru;
            }

            if ($pathsToClean && !Storage::disk('public')->delete($pathsToClean)) {
                Log::error('Gagal membersihkan gambar setelah pembaruan karya gagal.', [
                    'karya_id' => $karya->id,
                    'gambar' => $pathsToClean,
                ]);
            }

            throw $exception;
        }

        if ($gambarPathBaru && $gambarPathLama && !Storage::disk('public')->delete($gambarPathLama)) {
            Log::error('Gagal menghapus gambar utama lama setelah gambar utama karya diperbarui.', [
                'karya_id' => $karya->id,
                'gambar' => $gambarPathLama,
            ]);
        }

        return redirect()->route('karyamu.index')->with('success', 'Karya berhasil diperbarui');

    }

    public function destroyImage($id, string $gambar)
    {
        $karya = Karya::with('gambarTambahan')->where('user_id', Auth::id())->findOrFail($id);

        if ($gambar === 'utama') {
            abort_unless($karya->gambar, 404);

            $gambarPath = $karya->gambar;
            $gambarTambahan = $karya->gambarTambahan->first();

            if ($gambarTambahan) {
                $karya->gambar = $gambarTambahan->gambar;
                $gambarTambahan->delete();
            } else {
                $karya->gambar = null;
            }

            $karya->status_verifikasi = 'pending';
            $karya->save();
        } else {
            $gambarTambahan = $karya->gambarTambahan()->findOrFail($gambar);
            $gambarPath = $gambarTambahan->gambar;
            $gambarTambahan->delete();

            $karya->update(['status_verifikasi' => 'pending']);
        }

        if (!Storage::disk('public')->delete($gambarPath)) {
            Log::error('Gagal menghapus gambar karya dari penyimpanan.', [
                'karya_id' => $karya->id,
                'gambar' => $gambarPath,
            ]);

            return redirect()->route('karyamu.edit', $karya)
                ->with('error', 'Gambar berhasil dilepas dari karya, tetapi file gagal dihapus dari penyimpanan.');
        }

        return redirect()->route('karyamu.edit', $karya)
            ->with('success', 'Gambar berhasil dihapus.');
    }

    public function destroy($id)
    {
        // Cari berdasarkan user_id agar endpoint hapus tidak dapat dipakai lintas akun.
        $karya = Karya::with('gambarTambahan')->where('user_id', Auth::id())->findOrFail($id);
        $gambarPaths = $karya->semua_gambar->all();

        DB::transaction(function () use ($karya) {
            $pendingTransactionIds = TransaksiDetail::where('karya_id', $karya->id)
                ->whereHas('transaksi', fn ($query) => $query->where('transaction_status', 'pending'))
                ->pluck('transaksi_id')
                ->unique();

            $pendingTransactions = Transaksi::whereIn('id', $pendingTransactionIds)
                ->lockForUpdate()
                ->get()
                ->where('transaction_status', 'pending');

            foreach ($pendingTransactions as $transaksi) {
                $transaksi->update([
                    'transaction_status' => 'cancel',
                    'payment_type' => 'item_deleted',
                ]);
            }

         
            $karya->delete();
        });

      
        if ($gambarPaths && !Storage::disk('public')->delete($gambarPaths)) {
            Log::error('Gagal menghapus gambar karya setelah karya dihapus.', [
                'karya_id' => $id,
                'gambar' => $gambarPaths,
            ]);

            return redirect()->route('karyamu.index')->with(
                'error',
                'Karya berhasil dihapus, tetapi beberapa gambar di penyimpanan gagal dihapus.'
            );
        }

        return redirect()->route('karyamu.index')->with('success', 'Karya berhasil dihapus');
    }
}

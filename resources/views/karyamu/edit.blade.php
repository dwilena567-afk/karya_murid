@extends('layouts.main')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary-custom m-0">Edit Karya</h4>
    </div>

<div class="container" style="max-width: 800px;">
   

    <div class="mb-4">
        <a href="{{ route('karyamu.index') }}" class="btn btn-outline-secondary btn-sm">
            &larr; Kembali 
        </a>
    </div>

    <div class="card border-custom shadow-sm" style="border-radius: 10px;">
        <div class="card-body p-4">
            <form id="update-karya-form" action="{{ route('karyamu.update', $karya->id) }}" method="POST" enctype="multipart/form-data"
                data-confirm-price-zero="Harga Rp0 berarti karya ini akan ditandai sebagai gratis. Apakah Anda ingin melanjutkan?">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Judul Karya</label>
                        <input type="text" name="judul" class="form-control border-custom" value="{{ old('judul', $karya->judul) }}" maxlength="30" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Kategori</label>
                        <select name="kategori_id" class="form-select border-custom" required>
                            @foreach($kategoris as $kategori)
                                <option value="{{ $kategori->id }}" {{ (old('kategori_id', $karya->kategori_id) == $kategori->id) ? 'selected' : '' }}>{{ $kategori->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Harga (Rp)</label>
                        <input type="number" name="harga" class="form-control border-custom" value="{{ old('harga', $karya->harga) }}" min="0" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Stok</label>
                        <input type="number" name="stok" class="form-control border-custom" value="{{ old('stok', $karya->stok) }}" min="1" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Deskripsi Karya</label>
                        <textarea name="deskripsi" rows="4" class="form-control border-custom" required>{{ old('deskripsi', $karya->deskripsi) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Gambar Utama</label>
                        <input type="file" name="gambar" class="form-control border-custom @error('gambar') is-invalid @enderror" accept="image/*">
                        <small class="text-muted">Pilih jika ingin mengganti gambar utama.</small>
                        @error('gambar')
                            <div class="alert alert-danger small py-2 mt-2 mb-0">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Tambah Gambar</label>
                        <input type="file" name="gambar_tambahan[]" class="form-control border-custom @error('gambar_tambahan') is-invalid @enderror @error('gambar_tambahan.*') is-invalid @enderror"
                            accept="image/jpeg,image/png" multiple {{ $karya->semua_gambar->count() >= 5 ? 'disabled' : '' }}>
                        <small class="text-muted">Pilih hingga {{ max(0, 5 - $karya->semua_gambar->count()) }} gambar baru. Maksimal 5 gambar untuk satu karya, 2 MB per gambar.</small>
                        @error('gambar_tambahan')
                            <div class="alert alert-danger small py-2 mt-2 mb-0">{{ $message }}</div>
                        @enderror
                        @error('gambar_tambahan.*')
                            <div class="alert alert-danger small py-2 mt-2 mb-0">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </form>
            <div class="mt-3">
                <small class="text-muted d-block mb-2">Gambar saat ini:</small>
                <div class="d-flex flex-wrap gap-3">
                    @if($karya->gambar)
                        <div class="text-center">
                            <div class="clickable-gallery mb-2" style="cursor: pointer;">
                                <img src="{{ asset('storage/' . $karya->gambar) }}" alt="Gambar utama {{ $karya->judul }}"
                                    class="image-card rounded border border-custom object-fit-cover" style="width: 100px; height: 100px;">
                            </div>
                            <form action="{{ route('karyamu.gambar.destroy', [$karya->id, 'utama']) }}" method="POST"
                                data-confirm="Hapus gambar utama ini? Jika ada gambar lain, gambar berikutnya akan menjadi gambar utama."
                                data-confirm-title="Hapus Gambar">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm fw-bold w-100 w-md-auto">Hapus</button>
                            </form>
                        </div>
                    @endif
                    @foreach($karya->gambarTambahan as $gambar)
                        <div class="text-center">
                            <div class="clickable-gallery mb-2" style="cursor: pointer;">
                                <img src="{{ asset('storage/' . $gambar->gambar) }}" alt="Foto tambahan {{ $loop->iteration }} - {{ $karya->judul }}"
                                    class="image-card rounded border border-custom object-fit-cover" style="width: 100px; height: 100px;">
                            </div>
                            <form action="{{ route('karyamu.gambar.destroy', [$karya->id, $gambar->id]) }}" method="POST"
                                data-confirm="Hapus gambar ini dari karya?" data-confirm-title="Hapus Gambar">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm fw-bold w-100 w-md-auto">Hapus</button>
                            </form>
                        </div>
                    @endforeach
                    @unless($karya->gambar || $karya->gambarTambahan->isNotEmpty())
                        <img src="https://via.placeholder.com/100x100?text=Tidak+ada+gambar"
                            alt="Tidak ada gambar" class="rounded border border-custom" style="width: 100px; height: 100px;">
                    @endunless
                </div>
            </div>
            <div class="mt-4 text-end">
                <button type="submit" form="update-karya-form" class="btn bg-primary-custom text-white fw-bold px-4">Perbarui Karya</button>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.main')

@section('content')
<div class="container" style="max-width: 800px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary-custom m-0"><i class="bi bi-pencil-square me-2"></i>Edit Karya</h4>
        <a href="{{ route('karyamu.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Batal</a>
    </div>

    <div class="card border-custom shadow-sm" style="border-radius: 10px;">
        <div class="card-body p-4">
            <form action="{{ route('karyamu.update', $karya->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Judul Karya</label>
                        <input type="text" name="judul" class="form-control border-custom" value="{{ old('judul', $karya->judul) }}" required>
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
                        <label class="form-label fw-bold">Gambar Karya (Biarkan kosong jika tidak diganti)</label>
                        <input type="file" name="gambar" class="form-control border-custom" accept="image/*">
                        <div class="mt-2">
                            <small class="text-muted">Gambar saat ini:</small><br>
                            <img src="{{ asset('storage/' . $karya->gambar) }}" class="rounded mt-1 border border-custom" style="height: 100px;">
                        </div>
                    </div>
                </div>
                <div class="mt-4 text-end">
                    <button type="submit" class="btn bg-primary-custom text-white fw-bold px-4">Perbarui Karya</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
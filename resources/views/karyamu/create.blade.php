@extends('layouts.main')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary-custom m-0">Buat Karya</h4>
    </div>

<div class="container" style="max-width: 800px;">
    <div class="mb-4">
        <a href="{{ route('karyamu.index') }}" class="btn btn-outline-secondary btn-sm">
            &larr; Kembali 
        </a>
    </div>

    <div class="card border-custom shadow-sm" style="border-radius: 10px;">
        <div class="card-body p-4">
            <form action="{{ route('karyamu.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Judul Karya</label>
                        <input type="text" name="judul" class="form-control border-custom @error('judul') is-invalid @enderror" value="{{ old('judul') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Kategori</label>
                        <select name="kategori_id" class="form-select border-custom @error('kategori_id') is-invalid @enderror" required>
                            <option value="">-- Pilih --</option>
                            @foreach($kategoris as $kategori)
                                <option value="{{ $kategori->id }}" {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>{{ $kategori->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Harga (Rp)</label>
                        <input type="number" name="harga" class="form-control border-custom" value="{{ old('harga') }}" min="0" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Stok</label>
                        <input type="number" name="stok" class="form-control border-custom" value="{{ old('stok', 1) }}" min="1" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Deskripsi Karya</label>
                        <textarea name="deskripsi" rows="4" class="form-control border-custom" required>{{ old('deskripsi') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Gambar Karya</label>
                        <input type="file" name="gambar" class="form-control border-custom @error('gambar') is-invalid @enderror" accept="image/*" required>
                        @error('gambar')
                            <div class="alert alert-danger small py-2 mt-2 mb-0">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mt-4 text-end">
                    <button type="submit" class="btn bg-accent fw-bold px-4">Simpan Karya</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
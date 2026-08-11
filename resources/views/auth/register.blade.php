@extends('layouts.main')

@section('content')
<div class="row justify-content-center align-items-center w-100 m-0" style="min-height: 75vh;">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm" style="border: 1px solid var(--wisteria-blue); border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-primary-custom text-white text-center py-3 border-0">
                <i class="bi bi-person-plus fs-3"></i>
                <h5 class="mb-0 fw-bold mt-1">Buat Akun Baru</h5>
            </div>
            <div class="card-body p-4 bg-white">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label text-muted fw-bold small">Nama Lengkap</label>
                        <input id="name" type="text" class="form-control border-custom @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div class="mb-3">
                        <label for="email" class="form-label text-muted fw-bold small">Email</label>
                        <input id="email" type="email" class="form-control border-custom @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="username">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label text-muted fw-bold small">Password</label>
                        <input id="password" type="password" class="form-control border-custom @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label text-muted fw-bold small">Konfirmasi Password</label>
                        <input id="password_confirmation" type="password" class="form-control border-custom" name="password_confirmation" required autocomplete="new-password">
                    </div>

                    <div class="d-flex flex-column gap-3 mt-4">
                        <button type="submit" class="btn bg-accent fw-bold text-white shadow-sm w-100">
                            Daftar Sekarang
                        </button>

                        <div class="text-center small">
                            <span class="text-muted">Sudah punya akun?</span>
                            <a class="text-decoration-none ms-1 fw-bold" style="color: var(--steel-blue);" href="{{ route('login') }}">
                                Sign In di sini
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
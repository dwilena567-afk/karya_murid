@extends('layouts.main')

@section('content')
<div class="row justify-content-center align-items-center w-100 m-0" style="min-height: 75vh;">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm" style="border: 1px solid var(--wisteria-blue); border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-primary-custom text-white text-center py-3 border-0">
                <i class="bi bi-box-arrow-in-right fs-3"></i>
                <h5 class="mb-0 fw-bold mt-1">Selamat Datang Kembali</h5>
            </div>
            <div class="card-body p-4 bg-white">
                <!-- Session Status -->
                @if (session('status'))
                    <div class="alert alert-success small py-2">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="mb-3">
                        <label for="email" class="form-label text-muted fw-bold small">Email</label>
                        <input id="email" type="email" class="form-control border-custom @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label text-muted fw-bold small">Password</label>
                        <input id="password" type="password" class="form-control border-custom @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-3 form-check">
                        <input id="remember_me" type="checkbox" class="form-check-input border-custom" name="remember">
                        <label for="remember_me" class="form-check-label small text-muted">Ingat Saya</label>
                    </div>

                    <div class="d-flex flex-column gap-3 mt-4">
                        <button type="submit" class="btn bg-accent fw-bold text-white shadow-sm w-100">
                            Sign In
                        </button>

                       <div class="text-center small">
                            <span class="text-muted">Belum punya akun?</span>
                            <a class="text-decoration-none ms-1 fw-bold" style="color: var(--steel-blue);" href="{{ route('register') }}">
                                Daftar di sini
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
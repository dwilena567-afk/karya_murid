<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login DAN memiliki role 'admin'
        // PENTING: Sesuaikan 'role' dan 'admin' dengan nama kolom di tabel users Anda!
        // Misalnya jika pakai boolean: auth()->user()->is_admin == true
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        // Jika bukan admin, tampilkan error 403 Forbidden
        abort(403, 'Akses Ditolak. Halaman ini khusus Admin/Guru.');
    }
}
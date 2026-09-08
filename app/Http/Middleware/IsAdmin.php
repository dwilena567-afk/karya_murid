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
        // Middleware auth pada route memastikan user login; pengecekan ini menambah batas role admin.
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        // Jangan teruskan request agar endpoint verifikasi tidak dapat diakses user biasa.
        abort(403, 'Akses Ditolak. Halaman ini khusus Admin/Guru.');
    }
}
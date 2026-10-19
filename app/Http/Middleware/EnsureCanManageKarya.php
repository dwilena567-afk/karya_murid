<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCanManageKarya
{
    public function handle(Request $request, Closure $next): Response
    {
        if (in_array($request->user()?->role, ['murid', 'admin'], true)) {
            return $next($request);
        }

        return redirect()->route('katalog.index');
    }
}

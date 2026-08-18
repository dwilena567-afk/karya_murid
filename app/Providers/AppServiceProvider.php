<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use App\Models\Kategori;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
   public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        RedirectIfAuthenticated::redirectUsing(fn ($request) => route('beranda.index'));

        // Bagikan data kategori ke layout main agar bisa dipakai di sidebar
        View::composer('layouts.main', function ($view) {
            $view->with('kategoris', Kategori::all());
        });
    }

    
}

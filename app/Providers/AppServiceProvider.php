<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
        // View Composer untuk membagikan jumlah item keranjang ke semua view
        View::composer('*', function ($view) {
            $cart = session()->get('cart', []);

            // LOGIKA KUNCI:
            // count($cart) -> Menghitung ada berapa JENIS produk (Cumi Flower saja = 1)
            // sum('qty')   -> Menghitung total BERAT (Cumi Flower 10kg = 10) -> JANGAN PAKAI INI
            $cartCount = is_array($cart) ? count($cart) : 0;

            $view->with('cartCount', $cartCount);
        });
    }
}
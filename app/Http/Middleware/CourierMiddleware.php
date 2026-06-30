<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CourierMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Pastikan sudah login — tanpa ini Auth::user() akan null.
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // 2. Pastikan role-nya 'kurir' — role lain (admin/user) tidak punya akses halaman kurir.
        if (Auth::user()->role !== 'kurir') {
            return redirect()->route('user.dashboard')->with('error', 'Akses ditolak: bukan kurir.');
        }

        return $next($request);
    }
}

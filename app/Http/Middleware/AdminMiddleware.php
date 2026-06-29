<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ✅ Pastikan ada ini
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek apakah sudah login?
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // 2. Cek apakah role-nya admin?
        if (Auth::user()->role !== 'admin') {
            // Kalau login tapi bukan admin (misal user biasa), tendang ke dashboard user
            return redirect()->route('user.dashboard');
        }

        return $next($request);
    }
}
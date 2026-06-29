<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Jika belum login
        if (!$request->session()->has('logged_in')) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // Jika bukan user
        if ($request->session()->get('role') !== 'user') {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses sebagai user.');
        }

        return $next($request);
    }
}

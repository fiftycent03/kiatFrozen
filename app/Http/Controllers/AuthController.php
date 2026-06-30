<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function showRegisterForm()
    {
        return view('register');
    }

    public function login(Request $request)
    {
        // 1. Validasi Input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // 3. Cek Role untuk Redirect — urutan penting: cek role spesifik dulu,
            //    baru fallback ke dashboard user untuk semua role lain (termasuk 'user').
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            // Kurir diarahkan ke portal pengantaran, bukan dashboard customer.
            if (Auth::user()->role === 'kurir') {
                return redirect()->route('courier.dashboard');
            }

            // Default: semua role lain (user biasa, guest terdaftar, dsb.) ke dashboard user.
            return redirect()->route('user.dashboard');
        }

        return back()->with('error', 'Email atau password salah.');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        return redirect()->route('login')->with('success', 'Akun berhasil dibuat, silakan login.');
    }

    public function logout(Request $request)
    {
        Auth::logout(); 
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Berhasil logout.');
    }
}
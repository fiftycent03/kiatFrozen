<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->search, function ($query, $search) {
                return $query->where('name', 'LIKE', "%{$search}%")
                             ->orWhere('email', 'LIKE', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function couriers()
    {
        $couriers = User::where('role', 'kurir')->latest()->get();
        return view('admin.couriers.index', compact('couriers'));
    }

    public function storeCourier(Request $request)
    {
        // Validasi HANYA menerima name/email/password dari form — 'role' TIDAK termasuk
        // di sini, jadi walaupun ada field 'role' disisipkan lewat request (form tampering),
        // nilainya tidak pernah dipakai di bawah.
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            // Role di-hardcode 'kurir' di sini, BUKAN diambil dari $request->role.
            // Ini satu-satunya endpoint pembuatan akun kurir, jadi role harus dipaksa
            // di server agar tidak mungkin akun baru dari form ini tersimpan sebagai
            // role default ('user') atau role lain yang tidak diinginkan.
            'role'     => 'kurir',
        ]);

        return back()->with('success', "Akun kurir {$request->name} berhasil dibuat.");
    }

    public function destroyCourier(User $user)
    {
        if ($user->role !== 'kurir') {
            return back()->with('error', 'Hanya akun kurir yang bisa dihapus dari sini.');
        }

        $user->delete();
        return back()->with('success', 'Akun kurir berhasil dihapus.');
    }
}

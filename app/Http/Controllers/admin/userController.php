<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Tambahkan Request $request di dalam kurung ini
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
}
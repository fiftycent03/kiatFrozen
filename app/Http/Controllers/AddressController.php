<?php

namespace App\Http\Controllers;

use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Auth::user()->addresses()->latest()->get();
        // Daftar wilayah (provinsi/kota/kecamatan) kini diambil langsung dari API Emsifa
        // di sisi browser (lihat resources/views/user/address.blade.php), sehingga tidak
        // perlu lagi query ke tabel shipping_rates lokal di sini.
        return view('user.address', compact('addresses'));
    }

    public function store(Request $request)
{
    $request->validate([
        'label' => 'required',
        'customer_name' => 'required',
        'customer_phone' => 'required',
        'province' => 'required',
        'city' => 'required',
        'district' => 'required',
        'address_detail' => 'required',
    ]);

    // Ambil hanya data yang kita butuhkan, buang _token
    $data = $request->only([
        'label', 'customer_name', 'customer_phone', 
        'province', 'city', 'district', 'address_detail'
    ]);
    
    $data['user_id'] = Auth::id();

    // Logika Alamat Utama
    if (Auth::user()->addresses()->count() == 0 || $request->has('is_default')) {
        Auth::user()->addresses()->update(['is_default' => false]);
        $data['is_default'] = true;
    } else {
        $data['is_default'] = false;
    }

    // Sekarang aman untuk create
    UserAddress::create($data);

    return back()->with('success', 'Alamat baru berhasil ditambahkan!');
}

    public function setDefault(UserAddress $address)
    {
        Auth::user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);
        return back()->with('success', 'Alamat utama berhasil diubah.');
    }

    public function destroy(UserAddress $address)
    {
        $address->delete();
        return back()->with('success', 'Alamat berhasil dihapus.');
    }
}
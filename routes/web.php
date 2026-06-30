<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CourierController;

/*
|--------------------------------------------------------------------------
| 1. AUTHENTICATION (Login / Register / Logout)
|--------------------------------------------------------------------------
| Tetap terbuka. Login kini TIDAK lagi wajib untuk belanja — hanya
| dibutuhkan untuk fitur berbasis akun (buku alamat, riwayat) & area admin.
*/
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| 2. AREA PUBLIK (GUEST + USER) — TANPA LOGIN
|--------------------------------------------------------------------------
| Semua rute di bawah ini SENGAJA dibuka untuk publik agar pelanggan dapat
| menjelajah katalog dan menyelesaikan pembelian (guest checkout) tanpa login.
*/

// LANDING PAGE UTAMA: root '/' kini menampilkan dashboard user (view user.dashboardUser).
// Diarahkan ke controller, BUKAN view langsung, karena view membutuhkan data $products.
Route::get('/', [DashboardController::class, 'user'])->name('home');

// Dashboard user — kini PUBLIK. Nama 'user.dashboard' dipertahankan karena
// dipakai oleh banyak link di blade (header/navbar, riwayat, detail pesanan).
Route::get('/user/dashboard', [DashboardController::class, 'user'])->name('user.dashboard');

// Katalog produk per kategori — terbuka untuk publik.
Route::get('/produk/{kategori?}', [KatalogController::class, 'index'])->name('produk.kategori');

// Halaman statis "Tentang Kami" — terbuka untuk publik.
Route::get('/tentang-kami', function () { return view('user.tentangKami'); })->name('tentang.kami');

// Keranjang & Checkout — PUBLIK, inilah inti dukungan guest checkout.
Route::get('/cart', [OrderController::class, 'index'])->name('cart.index');         // lihat keranjang (tanpa login)
Route::get('/checkout', [OrderController::class, 'create'])->name('checkout.index'); // form checkout (tanpa login)
Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store'); // proses order (user_id boleh null)

// Operasional item keranjang — berbasis session, tidak butuh login — PUBLIK.
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/buy-now', [CartController::class, 'buyNow'])->name('cart.buyNow');
Route::post('/cart/{product}/qty', [CartController::class, 'updateQty'])->name('cart.updateQty');
Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('cart.remove');

// API Wilayah & Ongkir — PUBLIK agar kalkulasi ongkir di form checkout jalan tanpa login.
Route::get('/api/cities/{province}', [OrderController::class, 'getCities']);
Route::get('/api/districts/{city}', [OrderController::class, 'getDistricts']);
Route::post('/api/shipping-cost', [OrderController::class, 'getShippingCost'])->name('shipping.cost');

// Hasil & detail pesanan — PUBLIK agar guest dapat melihat konfirmasi & upload bukti bayar setelah checkout.
Route::get('/order/success/{id}', [OrderController::class, 'success'])->name('order.success');
Route::get('/user/order/{id}', [OrderController::class, 'show'])->name('order.show');
Route::post('/order/upload-proof/{id}', [OrderController::class, 'uploadProof'])->name('order.uploadProof');

// Konfirmasi pembayaran Midtrans via callback browser (pengganti webhook untuk localhost).
// Dipanggil oleh onSuccess Snap.js setelah transaksi berhasil di popup Midtrans.
Route::post('/payment/confirm', [OrderController::class, 'confirmPayment'])->name('payment.confirm');

/*
|--------------------------------------------------------------------------
| 3. AREA PRIVAT USER (WAJIB LOGIN)
|--------------------------------------------------------------------------
| Fitur yang terikat ke akun: mengandalkan Auth::user()/auth()->id(), tidak
| bermakna (atau error) untuk guest. Tetap dilindungi middleware 'auth'.
*/
Route::middleware(['auth'])->group(function () {
    // Riwayat pesanan milik user yang sedang login (memakai auth()->id()).
    Route::get('/user/riwayat', [DashboardController::class, 'riwayat'])->name('user.riwayat');

    // Konfirmasi pesanan diterima — memfilter berdasarkan Auth::id(), butuh login.
    Route::post('/user/order/{order}/confirm', [OrderController::class, 'confirmDelivered'])->name('order.confirm');

    // Buku Alamat (CRUD) — AddressController mengandalkan Auth::user(), wajib login.
    Route::get('/user/addresses', [AddressController::class, 'index'])->name('user.address.index');
    Route::post('/user/addresses', [AddressController::class, 'store'])->name('user.address.store');
    Route::delete('/user/addresses/{address}', [AddressController::class, 'destroy'])->name('user.address.destroy');
    Route::post('/user/addresses/{address}/set-default', [AddressController::class, 'setDefault'])->name('user.address.default');
});

/*
|--------------------------------------------------------------------------
| 4. AREA ADMIN (WAJIB LOGIN + ROLE ADMIN) — TETAP TERKUNCI
|--------------------------------------------------------------------------
| Sesuai instruksi, area admin TIDAK dibuka untuk publik. Dilindungi
| middleware 'admin' (cek login + role admin) seperti sebelumnya.
*/
Route::middleware(['admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    // Tandai semua notifikasi sebagai sudah dibaca — dipanggil JS saat dropdown lonceng dibuka.
    Route::post('/admin/notifications/read-all', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    })->name('admin.notifications.readAll');
    Route::resource('/admin/products', ProductController::class, ['as' => 'admin']);

    // Kelola Kategori (termasuk upload gambar kategori). Route 'store' juga dipakai
    // untuk tambah kategori INLINE via AJAX dari form Tambah Produk.
    Route::get('/admin/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/admin/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::put('/admin/categories/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/admin/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::put('/admin/orders/{id}', [AdminOrderController::class, 'update'])->name('admin.orders.update');
    Route::post('/orders/{order}/quick-process', [AdminOrderController::class, 'quickProcess'])->name('admin.orders.quick-process');
    Route::get('/admin/orders/{id}/download-proof', [AdminOrderController::class, 'downloadProof'])->name('admin.orders.download-proof');
    Route::get('/admin/sales', [AdminOrderController::class, 'salesReport'])->name('admin.sales.index');
    // Manajemen akun kurir.
    Route::get('/admin/couriers', [UserController::class, 'couriers'])->name('admin.couriers.index');
    Route::post('/admin/couriers', [UserController::class, 'storeCourier'])->name('admin.couriers.store');
    Route::delete('/admin/couriers/{user}', [UserController::class, 'destroyCourier'])->name('admin.couriers.destroy');
});

/*
|--------------------------------------------------------------------------
| 5. AREA KURIR (WAJIB LOGIN + ROLE KURIR)
|--------------------------------------------------------------------------
| Kurir hanya bisa lihat daftar pesanan 'shipped' dan upload bukti kirim.
| Tidak punya akses ke area admin maupun fitur akun user biasa.
*/
Route::middleware(['courier'])->prefix('kurir')->name('courier.')->group(function () {
    // Dashboard: daftar pesanan yang perlu diantarkan hari ini.
    Route::get('/dashboard', [CourierController::class, 'index'])->name('dashboard');
    // Upload foto bukti pengiriman untuk satu pesanan spesifik (by ID).
    Route::post('/orders/{id}/proof', [CourierController::class, 'updateDelivery'])->name('updateDelivery');
});

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order; 
use App\Models\Product; 
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    
   public function admin()
    {
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total');
        $revenueThisWeek = Order::where('payment_status', 'paid')
            ->whereBetween('paid_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('total');
        $revenueLastWeek = Order::where('payment_status', 'paid')
            ->whereBetween('paid_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])->sum('total');

        $revenuePercentage = 0;
        if ($revenueLastWeek > 0) {
            $revenuePercentage = (($revenueThisWeek - $revenueLastWeek) / $revenueLastWeek) * 100;
        } elseif ($revenueThisWeek > 0) { $revenuePercentage = 100; }

        // 2. PESANAN HARIAN
        $ordersToday = Order::whereDate('created_at', Carbon::today())->count();
        $ordersYesterday = Order::whereDate('created_at', Carbon::yesterday())->count();
        $orderPercentage = 0;
        if ($ordersYesterday > 0) {
            $orderPercentage = (($ordersToday - $ordersYesterday) / $ordersYesterday) * 100;
        } elseif ($ordersToday > 0) { $orderPercentage = 100; }

        // 3. TOTAL USER
        $totalUsers = User::where('role', '!=', 'admin')->count();
        $usersThisWeek = User::where('role', '!=', 'admin')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $usersLastWeek = User::where('role', '!=', 'admin')->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])->count();
        $userPercentage = 0;
        if ($usersLastWeek > 0) {
            $userPercentage = (($usersThisWeek - $usersLastWeek) / $usersLastWeek) * 100;
        } elseif ($usersThisWeek > 0) { $userPercentage = 100; }


        // =========================================
        // BAGIAN 2: DATA UNTUK CHART.JS (BARU!)
        // =========================================

        // A. Siapkan range tanggal 7 hari terakhir (H-6 sampai Hari Ini)
        $endDate = Carbon::today();
        $startDate = Carbon::today()->subDays(6);

        // B. Query Database: Ambil total penjualan per hari yang statusnya 'paid'
        // Hasilnya array asosiatif: ['2023-10-25' => 150000, '2023-10-27' => 300000]
        $salesDataRaw = Order::where('payment_status', 'paid')
            ->whereBetween('paid_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->groupBy(DB::raw('DATE(paid_at)')) // Kelompokkan berdasarkan tanggal saja (abaikan jam)
            ->orderBy(DB::raw('DATE(paid_at)'))
            ->get([
                DB::raw('DATE(paid_at) as date'),
                DB::raw('SUM(total) as total_sales')
            ])
            ->pluck('total_sales', 'date') // Ubah jadi format Key => Value
            ->toArray();

        // C. Siapkan Array Kosong untuk Label dan Data Chart
        $chartLabels = [];
        $chartData = [];

        // D. Looping 7 hari terakhir untuk mengisi data (mengisi kekosongan jika ada hari tanpa penjualan)
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateKey = $date->format('Y-m-d'); // Format kunci untuk pencarian array (misal: 2023-10-25)
            
            // Label Sumbu X (misal: "25 Okt")
            $chartLabels[] = $date->format('d M'); 

            // Data Sumbu Y: Cek apakah tanggal tersebut ada di hasil query? Jika tidak, isi 0.
            $chartData[] = $salesDataRaw[$dateKey] ?? 0;
        }

        // Kirim semua data ke View, termasuk data chart baru
        return view('admin.dashboardAdmin', compact(
            'totalRevenue', 'revenuePercentage',
            'ordersToday', 'orderPercentage',
            'totalUsers', 'userPercentage',
            'chartLabels', 'chartData' // <-- Tambahan Baru
        ));
    }

    public function user()
    {
        // Ambil 8 produk terbaru untuk ditampilkan di halaman depan
        // (Pastikan tabel products sudah ada isinya, kalau kosong nanti tidak error tapi tidak muncul apa-apa)
        $products = Product::latest()->take(8)->get();

        // Kirim data $products ke view dashboardUser
        return view('user.dashboardUser', compact('products'));
    }

    // 2. HALAMAN RIWAYAT PESANAN (FITUR BARU)
    // Kita pindahkan logika 'lihat pesanan' ke sini
    public function riwayat()
    {
        $userId = auth()->id();
        
        // Ambil pesanan milik user yang login
        $orders = Order::where('user_id', $userId)
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('user.riwayatPesan', compact('orders'));
    }
}
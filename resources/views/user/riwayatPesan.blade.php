
@extends('layouts.app')

@section('title', 'Riwayat Pesanan | KIAT Frozen Food')

@section('content')

<main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8 font-sans">

    {{-- HEADER HALAMAN --}}
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Riwayat Pesanan</h1>
            <p class="text-gray-500 mt-1">
                Pantau status pembayaran dan pengiriman paketmu di sini.
            </p>
        </div>
        <a href="{{ route('user.dashboard') }}" class="text-sm font-bold text-blue-600 hover:underline">
            ← Kembali ke Beranda Toko
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

        {{-- 1. SIDEBAR: PROFIL USER --}}
        <aside class="space-y-6">
            
            {{-- KARTU PROFIL --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 text-center md:text-left">
                <div class="flex flex-col md:flex-row items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center text-3xl">
                        👤
                    </div>
                    <div>
                        <div class="font-bold text-gray-900 text-lg">{{ Auth::user()->name }}</div>
                        <div class="text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                
                <hr class="border-gray-100 my-4">

                {{-- MENU SAMPING --}}
                <nav class="space-y-2">
                    {{-- Link Aktif (Sedang dibuka) --}}
                    <a href="{{ route('user.riwayat') }}" class="block px-4 py-3 bg-blue-600 text-white font-bold rounded-xl shadow-md transition transform scale-105">
                        📦 Riwayat Pesanan
                    </a>
                </nav>
            </div>

        </aside>

        {{-- 2. KONTEN UTAMA: TABEL PESANAN --}}
        <div class="md:col-span-3">

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h2 class="font-bold text-gray-700 flex items-center gap-2">
                        Daftar Transaksi
                    </h2>
                    <span class="text-xs font-semibold bg-gray-200 text-gray-600 px-2 py-1 rounded-full">
                        {{ $orders->count() }} Pesanan
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-xs uppercase text-gray-500 bg-white border-b border-gray-100">
                                <th class="px-6 py-4 font-semibold">Kode Order</th>
                                <th class="px-6 py-4 font-semibold">Tanggal</th>
                                <th class="px-6 py-4 font-semibold">Total</th>
                                <th class="px-6 py-4 font-semibold text-center">Status Bayar</th>
                                <th class="px-6 py-4 font-semibold text-center">Pengiriman</th>
                                <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($orders as $order)
                            <tr class="hover:bg-gray-50 transition group">
                                <td class="px-6 py-4 font-bold text-blue-600 group-hover:text-blue-700">
                                    #{{ $order->code }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $order->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-800">
                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                </td>
                                
                                {{-- STATUS BAYAR --}}
                                <td class="px-6 py-4 text-center">
                                    @if($order->payment_status == 'paid')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                            ✅ Lunas
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200 animate-pulse">
                                            ⏳ Pending
                                        </span>
                                    @endif
                                </td>

                                {{-- STATUS PENGIRIMAN --}}
                                <td class="px-6 py-4 text-center">
                                    @if($order->fulfillment_status == 'delivered')
                                        <span class="text-green-600 font-bold text-xs flex items-center justify-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Diterima
                                        </span>
                                    @elseif($order->fulfillment_status == 'shipped')
                                        <span class="text-blue-600 font-bold text-xs flex items-center justify-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                            Dikirim
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs font-medium bg-gray-100 px-2 py-1 rounded">
                                            Diproses
                                        </span>
                                    @endif
                                </td>

                                {{-- TOMBOL AKSI --}}
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('order.show', $order->id) }}" 
                                       class="inline-block bg-white border border-gray-200 text-gray-600 hover:border-blue-400 hover:text-blue-600 px-4 py-2 rounded-lg text-xs font-bold transition shadow-sm">
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <div class="text-6xl mb-4 opacity-30">📦</div>
                                        <h3 class="text-lg font-bold text-gray-600">Belum ada pesanan</h3>
                                        <p class="text-sm mt-1 mb-6">Kamu belum pernah belanja di sini.</p>
                                        <a href="{{ route('user.dashboard') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-full transition shadow-lg shadow-blue-200">
                                            Mulai Belanja
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
            </div>

        </div>

    </div>
</main>

@endsection
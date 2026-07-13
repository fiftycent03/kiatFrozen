
@extends('layouts.app')

@section('title', 'Riwayat Pesanan | KIAT Frozen Food')

@section('content')

{{-- Konten disesuaikan dengan Tema Abyss/Pearl/Gold: judul Fraunces/ink, kartu bg-white/90, --}}
{{-- aksen biru diganti lagoon/gold, badge status bayar/kirim tetap warna semantik. --}}
<main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8 font-sans">

    {{-- HEADER HALAMAN --}}
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h1 class="font-display text-3xl font-semibold text-ink">Riwayat Pesanan</h1>
            <p class="text-ink/50 mt-1">
                Pantau status pembayaran dan pengiriman paketmu di sini.
            </p>
        </div>
        <a href="{{ route('user.dashboard') }}" class="text-sm font-bold text-lagoon hover:text-gold transition">
            ← Kembali ke Beranda Toko
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

        {{-- 1. SIDEBAR: PROFIL USER --}}
        <aside class="space-y-6">

            {{-- KARTU PROFIL --}}
            <div class="bg-white/90 p-6 rounded-2xl shadow-sm border border-ink/5 text-center md:text-left">
                <div class="flex flex-col md:flex-row items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-lagoon/10 rounded-full flex items-center justify-center text-3xl">
                        👤
                    </div>
                    <div>
                        <div class="font-display font-semibold text-ink text-lg">{{ Auth::user()->name }}</div>
                        <div class="text-sm text-ink/50">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <hr class="border-ink/5 my-4">

                {{-- MENU SAMPING --}}
                <nav class="space-y-2">
                    {{-- Link Aktif (Sedang dibuka) — aksen emas --}}
                    <a href="{{ route('user.riwayat') }}" class="block px-4 py-3 bg-gold text-abyss font-bold rounded-xl shadow-glow transition">
                        📦 Riwayat Pesanan
                    </a>
                </nav>
            </div>

        </aside>

        {{-- 2. KONTEN UTAMA: TABEL PESANAN --}}
        <div class="md:col-span-3">

            <div class="bg-white/90 rounded-2xl shadow-sm border border-ink/5 overflow-hidden">
                <div class="px-6 py-5 border-b border-ink/5 bg-pearl flex justify-between items-center">
                    <h2 class="font-bold text-ink/80 flex items-center gap-2">
                        Daftar Transaksi
                    </h2>
                    <span class="text-xs font-semibold bg-ink/5 text-ink/60 px-2 py-1 rounded-full">
                        {{ $orders->count() }} Pesanan
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-xs uppercase text-ink/50 bg-white/70 border-b border-ink/10">
                                <th class="px-6 py-4 font-semibold">Kode Order</th>
                                <th class="px-6 py-4 font-semibold">Tanggal</th>
                                <th class="px-6 py-4 font-semibold">Total</th>
                                <th class="px-6 py-4 font-semibold text-center">Status Bayar</th>
                                <th class="px-6 py-4 font-semibold text-center">Pengiriman</th>
                                <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-ink/5">
                            @forelse($orders as $order)
                            <tr class="hover:bg-pearl/60 transition group">
                                <td class="px-6 py-4 font-bold text-lagoon group-hover:text-gold transition">
                                    #{{ $order->code }}
                                </td>
                                <td class="px-6 py-4 text-sm text-ink/60">
                                    {{ $order->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 font-bold text-ink font-mono">
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
                                        <span class="text-lagoon font-bold text-xs flex items-center justify-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                            Dikirim
                                        </span>
                                    @else
                                        <span class="text-ink/50 text-xs font-medium bg-ink/5 px-2 py-1 rounded">
                                            Diproses
                                        </span>
                                    @endif
                                </td>

                                {{-- TOMBOL AKSI --}}
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('order.show', $order->id) }}"
                                       class="inline-block bg-white border border-ink/10 text-ink/60 hover:border-gold hover:text-gold px-4 py-2 rounded-lg text-xs font-bold transition shadow-sm">
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-ink/40">
                                        <div class="text-6xl mb-4 opacity-30">📦</div>
                                        <h3 class="font-display text-lg font-semibold text-ink/70">Belum ada pesanan</h3>
                                        <p class="text-sm mt-1 mb-6">Kamu belum pernah belanja di sini.</p>
                                        {{-- CTA emas (gold) sesuai tema premium --}}
                                        <a href="{{ route('user.dashboard') }}" class="bg-gold hover:brightness-110 text-abyss font-bold py-2 px-6 rounded-full transition shadow-glow">
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
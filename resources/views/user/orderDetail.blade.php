@extends('layouts.app')

@section('content')
{{-- Logika Jam Operasional --}}
@php
    $jamSekarang = now()->format('H:i');
    $hariIni = now()->format('N'); // 1 (Senin) - 7 (Minggu)
    $jamBuka = '08:00';
    $jamTutup = '17:00';
    
    // Cek apakah di luar jam kerja (Tutup Minggu atau di luar jam 08-17)
    $isTutup = ($hariIni == 7 || $jamSekarang < $jamBuka || $jamSekarang > $jamTutup);
@endphp

<div class="max-w-5xl mx-auto px-4 py-8 font-sans">
    
    {{-- HEADER: JUDUL & TOMBOL KEMBALI --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                📄 Detail Pesanan
                <span class="text-sm font-normal bg-gray-100 px-3 py-1 rounded-full text-gray-500">
                    #{{ $order->code }}
                </span>
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Dipesan pada: {{ $order->created_at->format('d M Y, H:i') }}
            </p>
        </div>
        <a href="{{ route('user.dashboard') }}" class="text-sm font-bold text-gray-600 hover:text-blue-600 flex items-center gap-1 transition">
            ← Kembali ke Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- KOLOM KIRI: DAFTAR PRODUK --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- 1. DAFTAR BARANG --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                    <h3 class="font-bold text-gray-700 text-sm uppercase tracking-wide">Produk Dibeli</h3>
                </div>
                
                <div class="p-0">
                    @foreach($order->items as $item)
                    <div class="flex justify-between items-center p-6 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center text-2xl">
                                ❄️
                            </div>
                            <div>
                                <div class="font-bold text-gray-800 text-lg">{{ $item->name_snapshot }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ $item->qty }} x Rp {{ number_format($item->price_per_kg_snapshot, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                        <div class="font-bold text-gray-900">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- RINCIAN HARGA --}}
                <div class="bg-gray-50 px-6 py-6 border-t border-gray-200 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal Produk</span>
                        <span class="font-semibold text-gray-900">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Ongkos Kirim ({{ ucfirst($order->shipping_service) }})</span>
                        <span class="font-semibold text-gray-900">Rp {{ number_format($order->shipping_fee, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-4 border-t border-gray-300">
                        <span class="text-lg font-bold text-gray-800">Total Bayar</span>
                        <span class="text-xl font-bold text-blue-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- 2. UPLOAD BUKTI PEMBAYARAN --}}
            @if($order->payment_channel == 'transfer' && $order->payment_status != 'paid')
            <div class="bg-white rounded-xl shadow-sm border-2 border-blue-100 p-6">
                <h3 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
                    📸 Bukti Pembayaran
                </h3>
                
                @if($order->payment_proof)
                    <div class="mb-4 p-4 bg-green-50 rounded-lg border border-green-100 flex items-center gap-4">
                        <img src="{{ asset('storage/' . $order->payment_proof) }}" class="w-20 h-20 object-cover rounded-md border-2 border-white shadow-sm">
                        <div>
                            <p class="text-sm font-bold text-green-700">Bukti Sudah Terkirim</p>
                            <p class="text-xs text-green-600">Admin akan segera memverifikasi pesanan Anda.</p>
                        </div>
                    </div>
                @endif

                <form action="{{ route('order.uploadProof', $order->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="flex flex-col md:flex-row gap-4 items-end">
                        <div class="flex-1 w-full">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Pilih Foto Bukti Transfer</label>
                            <input type="file" name="payment_proof" accept="image/*" required 
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor:pointer border border-gray-200 rounded-xl p-2">
                        </div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold transition shadow-lg shadow-blue-100 whitespace-nowrap">
                            Unggah Sekarang
                        </button>
                    </div>
                </form>
            </div>
            @endif

            {{-- 3. STATUS PENGIRIMAN & KONFIRMASI --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
                    🚚 Status Pengiriman
                </h3>
                
                <div class="flex flex-col gap-4">
                    <div class="flex items-start gap-4">
                        <div class="mt-1">
                            {{-- Titik hijau = selesai, biru berkedip = masih aktif --}}
                            @if(in_array($order->fulfillment_status, ['delivered', 'received']))
                                <span class="flex h-3 w-3 rounded-full bg-green-500"></span>
                            @else
                                <span class="flex h-3 w-3 rounded-full bg-blue-500 animate-pulse"></span>
                            @endif
                        </div>
                        <div>
                            <div class="font-bold text-gray-800 uppercase text-lg">
                                @if($order->fulfillment_status == 'pending')
                                    Menunggu Proses
                                @elseif($order->fulfillment_status == 'processing')
                                    Sedang Disiapkan
                                @elseif($order->fulfillment_status == 'shipped')
                                    Dalam Perjalanan
                                @elseif($order->fulfillment_status == 'delivered')
                                    Tiba di Tujuan
                                @elseif($order->fulfillment_status == 'received')
                                    Pesanan Selesai
                                @endif
                            </div>
                            <p class="text-sm text-gray-500">
                                Metode: {{ $order->shipping_service == 'standard' ? '📦 Standar' : '⚡ Kilat' }}
                            </p>
                        </div>
                    </div>

                    {{-- ============================================================ --}}
                    {{-- LOGIKA BUKTI PENGIRIMAN + TOMBOL KONFIRMASI --}}
                    {{-- --}}
                    {{-- Alur baru (Proof of Delivery): --}}
                    {{-- 1. Admin set 'shipped'  → pesanan di tangan kurir --}}
                    {{-- 2. Kurir upload foto    → status jadi 'delivered', delivery_proof terisi --}}
                    {{-- 3. Customer lihat foto  → tombol konfirmasi BARU muncul di sini --}}
                    {{-- 4. Customer klik konfirmasi → status jadi 'received' (selesai) --}}
                    {{-- ============================================================ --}}

                    {{-- Foto bukti pengiriman: tampil untuk status 'delivered' DAN 'received'. --}}
                    {{-- Setelah customer konfirmasi (received), foto tetap bisa dilihat sebagai riwayat. --}}
                    @if($order->delivery_proof && in_array($order->fulfillment_status, ['delivered', 'received']))
                    <div class="mt-2 space-y-4">
                        {{-- Foto bukti dari kurir — selalu tampil selama ada foto --}}
                        <div class="p-4 bg-green-50 rounded-xl border border-green-200">
                            <p class="text-xs font-bold text-green-700 uppercase tracking-wide mb-3">
                                📸 Bukti Pengiriman dari Kurir
                            </p>
                            <img src="{{ asset('storage/' . $order->delivery_proof) }}"
                                 alt="Bukti pengiriman"
                                 class="w-full max-h-56 object-cover rounded-lg border border-green-200 shadow-sm mb-3">
                            {{-- Waktu tiba — delivered_at di-cast ke Carbon di Order model --}}
                            <p class="text-xs text-green-600 font-semibold">
                                🕐 Tiba: {{ \Carbon\Carbon::parse($order->delivered_at)->format('d M Y, H:i') }}
                            </p>
                        </div>

                        {{-- Tombol konfirmasi: HANYA untuk 'delivered' (belum dikonfirmasi customer) --}}
                        @if($order->fulfillment_status == 'delivered')
                        <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
                            <p class="text-xs text-blue-800 font-semibold mb-3">
                                ℹ️ Sudah menerima barang sesuai foto di atas? Konfirmasi untuk menutup pesanan.
                            </p>
                            <form action="{{ route('order.confirm', $order->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    onclick="return confirm('Konfirmasi bahwa pesanan sudah Anda terima?')"
                                    class="w-full py-3 rounded-xl font-bold shadow-lg transition transform hover:-translate-y-1 bg-green-600 hover:bg-green-700 text-white shadow-green-100">
                                    ✅ Konfirmasi Pesanan Diterima
                                </button>
                            </form>
                        </div>
                        @else
                        {{-- Status 'received': foto tetap tampil, tombol sudah hilang --}}
                        <div class="p-4 bg-green-100 rounded-xl border border-green-200 text-center">
                            <p class="font-bold text-green-700">🎉 Pesanan Selesai</p>
                            <p class="text-xs text-green-600 mt-1">Anda sudah mengonfirmasi penerimaan barang ini.</p>
                        </div>
                        @endif
                    </div>

                    {{-- Kasus: Dalam perjalanan (shipped), kurir belum upload bukti --}}
                    @elseif($order->fulfillment_status == 'shipped')
                    <div class="mt-2 p-4 bg-amber-50 rounded-xl border border-amber-200">
                        <p class="text-xs text-amber-800 font-semibold">
                            🚚 Pesanan sedang dalam perjalanan. Tombol konfirmasi akan muncul setelah kurir
                            mengunggah foto bukti pengiriman.
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN --}}
        <div class="space-y-6">
            
            {{-- INFO PENERIMA --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-bold text-gray-700 mb-4 border-b pb-3">📍 Alamat Pengiriman</h3>
                <div class="space-y-4 text-sm">
                    <div>
                        <span class="block text-gray-400 text-xs uppercase font-bold mb-1">Penerima</span>
                        <span class="font-semibold text-gray-800 text-base">{{ $order->customer_name }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-400 text-xs uppercase font-bold mb-1">Kontak</span>
                        <span class="font-semibold text-gray-800">{{ $order->customer_phone }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-400 text-xs uppercase font-bold mb-1">Alamat Lengkap</span>
                        <div class="text-gray-700 leading-relaxed bg-gray-50 p-3 rounded-md border border-gray-100">
                            {{ $order->customer_address }}<br>
                            <span class="font-bold text-blue-600 mt-2 block">{{ $order->district }}, {{ $order->city }}</span>
                            <span class="text-xs text-gray-400">{{ $order->province }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STATUS PEMBAYARAN --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                <h3 class="font-bold text-gray-700 mb-4">Status Pembayaran</h3>

                {{-- Badge status: hijau = lunas, kuning = COD, oranye = pending Midtrans, merah = belum bayar --}}
                @if($order->payment_status == 'paid')
                    <div class="bg-green-100 text-green-700 px-4 py-3 rounded-xl font-bold mb-2 flex items-center justify-center gap-2 border border-green-200">
                        ✅ SUDAH DIBAYAR / LUNAS
                    </div>
                    @if($order->paid_at)
                    {{-- paid_at kini di-cast ke Carbon di Order::$casts, ->format() aman dipanggil. --}}
                    {{-- Fallback \Carbon\Carbon::parse() sebagai lapisan aman bila nilai lama tersimpan sebagai string. --}}
                    <p class="text-xs text-gray-400 mb-3">Dikonfirmasi: {{ \Carbon\Carbon::parse($order->paid_at)->format('d M Y, H:i') }}</p>
                    @endif
                @elseif($order->payment_channel == 'cod')
                    <div class="bg-yellow-50 text-yellow-700 px-4 py-3 rounded-xl font-bold border border-yellow-200 text-sm uppercase">
                        📦 BAYAR DI TEMPAT (COD)
                    </div>
                @elseif($order->payment_channel == 'midtrans')
                    {{-- Midtrans belum dibayar: arahkan ke halaman sukses untuk popup ulang --}}
                    <div class="bg-orange-50 text-orange-700 px-4 py-3 rounded-xl font-bold border border-orange-200 text-sm uppercase mb-3">
                        🕐 MENUNGGU PEMBAYARAN
                    </div>
                    <a href="{{ route('order.success', $order->id) }}"
                       class="block w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-bold text-sm transition shadow-lg shadow-blue-100">
                        💳 Lanjutkan Pembayaran
                    </a>
                    <p class="text-xs text-gray-400 mt-2">Klik untuk membuka kembali popup Midtrans</p>
                @else
                    <div class="bg-red-50 text-red-700 px-4 py-3 rounded-xl font-bold border border-red-200 text-sm uppercase">
                        ⚠️ MENUNGGU PEMBAYARAN
                    </div>
                @endif

                <div class="mt-4 p-3 bg-gray-50 rounded-lg text-left">
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Metode:</span>
                    <p class="text-sm font-bold text-gray-700">
                        @if($order->payment_channel == 'midtrans') 💳 Midtrans (Online)
                        @elseif($order->payment_channel == 'cod') 📦 Bayar di Tempat
                        @else {{ strtoupper($order->payment_channel) }}
                        @endif
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
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

{{-- Konten disesuaikan dengan Tema Abyss/Pearl/Gold: judul Fraunces/ink, kartu bg-white/90, --}}
{{-- aksen biru dekoratif diganti lagoon, tombol bayar memakai gold. Badge status --}}
{{-- (lunas/pending/COD) tetap memakai warna semantik agar mudah dikenali. --}}
<div class="max-w-5xl mx-auto px-4 py-8 font-sans">

    {{-- HEADER: JUDUL & TOMBOL KEMBALI --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="font-display text-2xl font-semibold text-ink flex items-center gap-2">
                📄 Detail Pesanan
                <span class="text-sm font-normal font-mono bg-ink/5 px-3 py-1 rounded-full text-ink/60">
                    #{{ $order->code }}
                </span>
            </h1>
            <p class="text-sm text-ink/50 mt-1">
                Dipesan pada: {{ $order->created_at->format('d M Y, H:i') }}
            </p>
        </div>
        <a href="{{ route('user.dashboard') }}" class="text-sm font-bold text-ink/60 hover:text-lagoon flex items-center gap-1 transition">
            ← Kembali ke Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- KOLOM KIRI: DAFTAR PRODUK --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- 1. DAFTAR BARANG --}}
            <div class="bg-white/90 rounded-xl shadow-sm border border-ink/10 overflow-hidden">
                <div class="bg-pearl px-6 py-3 border-b border-ink/10">
                    <h3 class="font-bold text-ink/80 text-sm uppercase tracking-wide">Produk Dibeli</h3>
                </div>

                <div class="p-0">
                    @foreach($order->items as $item)
                    <div class="flex justify-between items-center p-6 border-b border-ink/5 last:border-0 hover:bg-pearl/60 transition">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-lagoon/10 rounded-lg flex items-center justify-center text-2xl">
                                ❄️
                            </div>
                            <div>
                                <div class="font-display font-semibold text-ink text-lg">{{ $item->name_snapshot }}</div>
                                <div class="text-sm text-ink/50 font-mono">
                                    {{ $item->qty }} x Rp {{ number_format($item->price_per_kg_snapshot, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                        <div class="font-bold text-ink font-mono">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- RINCIAN HARGA --}}
                <div class="bg-pearl px-6 py-6 border-t border-ink/10 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-ink/60">Subtotal Produk</span>
                        <span class="font-semibold text-ink font-mono">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-ink/60">Ongkos Kirim ({{ ucfirst($order->shipping_service) }})</span>
                        <span class="font-semibold text-ink font-mono">Rp {{ number_format($order->shipping_fee, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-4 border-t border-ink/15">
                        <span class="text-lg font-bold text-ink">Total Bayar</span>
                        <span class="text-xl font-bold text-lagoon font-mono">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- 2. UPLOAD BUKTI PEMBAYARAN (Transfer Bank Manual) --}}
            {{-- LOGIKA STATUS: form upload hanya relevan selama pesanan BELUM lunas.
                 - 'awaiting_verification': sudah upload, form disembunyikan (tunggu Admin).
                 - 'pending'/'rejected': form ditampilkan (belum upload / bukti ditolak, upload ulang).
                 - 'paid': seksi ini disembunyikan total (lihat @unless di bawah). --}}
            @unless($order->payment_status === 'paid')
            <div class="bg-white/90 rounded-xl shadow-sm border-2 border-lagoon/20 p-6">
                <h3 class="font-bold text-ink/80 mb-4 flex items-center gap-2">
                    📸 Bukti Pembayaran
                </h3>

                @if($order->payment_status === 'rejected')
                    <div class="mb-4 p-4 bg-coral/10 rounded-lg border border-coral/20">
                        <p class="text-sm font-bold text-coral">⚠️ Bukti sebelumnya ditolak Admin.</p>
                        <p class="text-xs text-coral/80 mt-1">Silakan unggah ulang foto bukti transfer yang benar di bawah ini.</p>
                    </div>
                @endif

                @if($order->payment_proof)
                    <div class="mb-4 p-4 bg-green-50 rounded-lg border border-green-100 flex items-center gap-4">
                        <img src="{{ asset('storage/' . $order->payment_proof) }}" class="w-20 h-20 object-cover rounded-md border-2 border-white shadow-sm">
                        <div>
                            <p class="text-sm font-bold text-green-700">
                                {{ $order->payment_status === 'awaiting_verification' ? 'Bukti Sudah Terkirim' : 'Bukti Terakhir Diunggah' }}
                            </p>
                            <p class="text-xs text-green-600">
                                {{ $order->payment_status === 'awaiting_verification' ? 'Admin akan segera memverifikasi pesanan Anda.' : 'Bukti ini ditolak — unggah foto baru untuk mengganti.' }}
                            </p>
                        </div>
                    </div>
                @endif

                @if($order->payment_status === 'awaiting_verification')
                    {{-- Form disembunyikan selama masih ditinjau, supaya user tidak
                         upload ulang tanpa alasan sementara Admin sedang mengecek. --}}
                    <p class="text-xs text-ink/50 italic">🕐 Menunggu verifikasi Admin. Form upload akan muncul kembali jika bukti ini ditolak.</p>
                @else
                    <form action="{{ route('order.uploadProof', $order->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div class="flex flex-col md:flex-row gap-4 items-end">
                            <div class="flex-1 w-full">
                                <label class="block text-xs font-bold text-ink/40 uppercase mb-2">Pilih Foto Bukti Transfer</label>
                                <input type="file" name="payment_proof" accept="image/*" required
                                    class="block w-full text-sm text-ink/50 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-lagoon/10 file:text-lagoon hover:file:bg-lagoon/20 cursor:pointer border border-ink/10 rounded-xl p-2">
                            </div>
                            {{-- CTA emas (gold) sesuai tema premium --}}
                            <button type="submit" class="bg-gold hover:brightness-110 text-abyss px-6 py-2.5 rounded-xl font-bold transition shadow-glow whitespace-nowrap">
                                Unggah Sekarang
                            </button>
                        </div>
                    </form>
                @endif
            </div>
            @endunless

            {{-- 3. STATUS PENGIRIMAN & KONFIRMASI --}}
            <div class="bg-white/90 rounded-xl shadow-sm border border-ink/10 p-6">
                <h3 class="font-bold text-ink/80 mb-4 flex items-center gap-2">
                    🚚 Status Pengiriman
                </h3>

                <div class="flex flex-col gap-4">
                    <div class="flex items-start gap-4">
                        <div class="mt-1">
                            {{-- Titik hijau = selesai, lagoon berkedip = masih aktif --}}
                            @if(in_array($order->fulfillment_status, ['delivered', 'received']))
                                <span class="flex h-3 w-3 rounded-full bg-green-500"></span>
                            @else
                                <span class="flex h-3 w-3 rounded-full bg-lagoon animate-pulse"></span>
                            @endif
                        </div>
                        <div>
                            <div class="font-bold text-ink uppercase text-lg">
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
                            <p class="text-sm text-ink/50">
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
                        <div class="p-4 bg-lagoon/5 rounded-xl border border-lagoon/20">
                            <p class="text-xs text-lagoon font-semibold mb-3">
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
            <div class="bg-white/90 rounded-xl shadow-sm border border-ink/10 p-6">
                <h3 class="font-bold text-ink/80 mb-4 border-b border-ink/10 pb-3">📍 Alamat Pengiriman</h3>
                <div class="space-y-4 text-sm">
                    <div>
                        <span class="block text-ink/40 text-xs uppercase font-bold mb-1">Penerima</span>
                        <span class="font-semibold text-ink text-base">{{ $order->customer_name }}</span>
                    </div>
                    <div>
                        <span class="block text-ink/40 text-xs uppercase font-bold mb-1">Kontak</span>
                        <span class="font-semibold text-ink">{{ $order->customer_phone }}</span>
                    </div>
                    <div>
                        <span class="block text-ink/40 text-xs uppercase font-bold mb-1">Alamat Lengkap</span>
                        <div class="text-ink/70 leading-relaxed bg-pearl p-3 rounded-md border border-ink/10">
                            {{ $order->customer_address }}<br>
                            <span class="font-bold text-lagoon mt-2 block">{{ $order->district }}, {{ $order->city }}</span>
                            <span class="text-xs text-ink/40">{{ $order->province }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STATUS PEMBAYARAN --}}
            <div class="bg-white/90 rounded-xl shadow-sm border border-ink/10 p-6 text-center">
                <h3 class="font-bold text-ink/80 mb-4">Status Pembayaran</h3>

                {{-- Badge status: hijau = lunas, kuning = COD, gold = menunggu verifikasi
                     Admin, merah = belum bayar / bukti ditolak. Midtrans (biru) sudah
                     dihapus total — pembayaran kini murni Transfer Bank Manual. --}}
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
                @elseif($order->payment_status == 'awaiting_verification')
                    {{-- Bukti transfer sudah di-upload user, tinggal menunggu Admin klik
                         "ACC Pembayaran" di panel Admin (lihat Admin\OrderController@approvePayment). --}}
                    <div class="bg-gold/10 text-ink px-4 py-3 rounded-xl font-bold border border-gold/30 text-sm uppercase">
                        🕐 MENUNGGU VERIFIKASI ADMIN
                    </div>
                @elseif($order->payment_status == 'rejected')
                    <div class="bg-coral/10 text-coral px-4 py-3 rounded-xl font-bold border border-coral/25 text-sm uppercase">
                        ⚠️ BUKTI DITOLAK — UPLOAD ULANG
                    </div>
                @else
                    <div class="bg-red-50 text-red-700 px-4 py-3 rounded-xl font-bold border border-red-200 text-sm uppercase">
                        ⚠️ MENUNGGU PEMBAYARAN
                    </div>
                @endif

                <div class="mt-4 p-3 bg-pearl rounded-lg text-left">
                    <span class="text-[10px] font-bold text-ink/40 uppercase">Metode:</span>
                    <p class="text-sm font-bold text-ink/80">
                        @if($order->payment_channel == 'cod') 📦 Bayar di Tempat
                        @else 🏦 Transfer Bank Manual
                        @endif
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
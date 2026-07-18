@extends('layouts.app')

@section('title', 'Katalog Produk | KIAT Frozen Food')

@section('content')

{{-- Memasukkan Alpine.js via CDN jika belum ada di layout utama --}}
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
    /* Menghilangkan spinner input number */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    input[type=number] { -moz-appearance: textfield; }
    [x-cloak] { display: none !important; }
</style>

{{-- Konten disesuaikan dengan Tema Abyss/Pearl/Gold: judul Fraunces, kartu bg-white/90, --}}
{{-- aksen biru lama diganti lagoon, tombol keranjang memakai gold. --}}
<main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8 font-sans">

    <div class="mb-8">
        <h1 class="font-display text-3xl font-semibold text-ink">Katalog Produk</h1>
        <p class="text-ink/50 mt-1">
            Menampilkan produk: <span class="font-bold text-lagoon">{{ $kategori ? ucfirst($kategori) : 'Semua Produk' }}</span>
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

        {{-- SIDEBAR --}}
        <aside class="space-y-6">
            <div class="bg-white/90 p-5 rounded-2xl shadow-sm border border-ink/5">
                <h3 class="font-bold text-ink/80 mb-3 text-sm uppercase tracking-wider">Cari Produk</h3>
                <form method="GET" action="{{ route('produk.kategori', $kategori) }}">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ikan..."
                               class="w-full pl-4 pr-10 py-2.5 border border-ink/10 rounded-xl focus:ring-2 focus:ring-lagoon outline-none text-sm transition">
                        <button type="submit" class="absolute right-3 top-3 text-ink/40">🔍</button>
                    </div>
                </form>
            </div>

            <div class="bg-white/90 p-5 rounded-2xl shadow-sm border border-ink/5">
                <h3 class="font-bold text-ink/80 mb-3 text-sm uppercase tracking-wider">Kategori</h3>
                <nav class="space-y-1">
                    <a href="{{ route('produk.kategori') }}" class="block px-3 py-2 rounded-lg text-sm {{ !$kategori ? 'bg-lagoon/10 text-lagoon font-bold' : 'text-ink/60 hover:bg-pearl' }}">📂 Semua Produk</a>
                    {{-- Daftar kategori kini diambil langsung dari tabel categories (dinamis, sesuai data Admin), --}}
                    {{-- bukan lagi array hardcoded -> kategori baru seperti "Japanes" otomatis muncul di sini. --}}
                    @foreach($categories as $cat)
                        <a href="{{ route('produk.kategori', $cat->slug) }}" class="block px-3 py-2 rounded-lg text-sm {{ $kategori == $cat->slug ? 'bg-lagoon/10 text-lagoon font-bold' : 'text-ink/60 hover:bg-pearl' }}">🔹 {{ $cat->name }}</a>
                    @endforeach
                </nav>
            </div>
        </aside>

        {{-- GRID PRODUK --}}
        <div class="md:col-span-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                @forelse($products as $product)
                {{-- Field stok dihapus atas permintaan --}}
                <div class="bg-white/90 rounded-2xl shadow-sm border border-ink/5 overflow-hidden hover:shadow-md transition flex flex-col h-full"
                     x-data="{
                        qty: {{ $product->min_pembelian ?? 1 }},
                        min: {{ $product->min_pembelian ?? 1 }}
                     }">

                    {{-- IMAGE SECTION — dijadikan link ke Halaman Detail Produk --}}
                    <a href="{{ route('produk.show', $product->slug) }}" class="relative block h-48 bg-pearl">
                        @if($product->primaryImage)
                            <img src="{{ asset('storage/'.$product->primaryImage->path) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-ink/40 text-xs italic text-center p-4">Gambar belum tersedia</div>
                        @endif

                        <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-2 py-1 rounded text-[10px] font-bold text-ink/60 shadow-sm uppercase">
                            {{ $product->satuan ?? 'Kg' }}
                        </div>

                        {{-- Field stok dihapus atas permintaan --}}
                        <div class="absolute bottom-3 left-3">
                            @if($product->isKg())
                                {{-- Produk Kg: tampilkan jumlah varian --}}
                                <span class="bg-lagoon text-white text-[10px] px-2 py-1 rounded-full font-bold shadow-lg">{{ $product->variants->count() }} Varian</span>
                            @endif
                        </div>
                    </a>

                    {{-- DETAIL SECTION --}}
                    <div class="p-5 flex flex-col flex-1">
                        <div class="mb-4 flex-1">
                            <a href="{{ route('produk.show', $product->slug) }}" class="hover:text-lagoon transition">
                                <h3 class="font-display font-semibold text-ink text-lg leading-tight">{{ $product->name }}</h3>
                            </a>

                            @if($product->isPcs())
                                {{-- Info Minimal Pembelian (aksen coral, dipakai terbatas) --}}
                                <div class="inline-flex items-center gap-1.5 text-coral bg-coral/10 px-2 py-1 rounded-md text-[10px] font-bold uppercase mt-2 border border-coral/20">
                                    📌 Min. Beli: {{ $product->min_pembelian }} {{ $product->satuan }}
                                </div>

                                <div class="text-ink font-black text-xl mt-2 font-mono">
                                    Rp {{ number_format($product->price_per_kg, 0, ',', '.') }}
                                </div>
                            @else
                                {{-- Produk Kg: harga bervariasi per potongan -> tampilkan "mulai dari" --}}
                                <div class="text-ink font-black text-xl mt-2 font-mono">
                                    Mulai Rp {{ number_format($product->variants->min('price') ?? $product->price_per_kg, 0, ',', '.') }}
                                </div>
                            @endif
                        </div>

                        @if($product->isPcs())
                            {{-- ACTION FORM (Pcs): tidak berubah — qty stepper + Add to Cart langsung --}}
                            <form action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">

                                {{-- QUANTITY BUTTONS --}}
                                <div class="flex items-center justify-between bg-pearl rounded-xl p-1.5 border border-ink/10 mb-4">
                                    {{-- Tombol Kurangi --}}
                                    <button type="button"
                                            @click="if(qty > min) qty--"
                                            class="w-10 h-10 flex items-center justify-center bg-white rounded-lg text-ink/60 shadow-sm transition active:scale-95"
                                            :class="qty <= min ? 'opacity-30 cursor-not-allowed' : 'hover:bg-coral/10 hover:text-coral'">
                                        <span class="text-xl font-bold">-</span>
                                    </button>

                                    {{-- Angka Quantity (Input) --}}
                                    <input type="number"
                                           name="qty"
                                           x-model.number="qty"
                                           class="w-14 text-center bg-transparent border-none focus:ring-0 font-black text-ink text-lg"
                                           readonly>

                                    {{-- Tombol Tambah --}}
                                    <button type="button"
                                            @click="qty++"
                                            class="w-10 h-10 flex items-center justify-center bg-white rounded-lg text-ink/60 shadow-sm transition active:scale-95 hover:bg-lagoon/10 hover:text-lagoon">
                                        <span class="text-xl font-bold">+</span>
                                    </button>
                                </div>

                                {{-- Field stok dihapus atas permintaan --}}
                                <div class="flex flex-col gap-2">
                                    <button type="submit" class="w-full bg-gold hover:brightness-110 text-abyss py-3 rounded-xl text-sm font-bold transition shadow-glow active:scale-95 flex items-center justify-center gap-2">
                                        🛒 + Keranjang
                                    </button>
                                    <button type="submit" formaction="{{ route('cart.buyNow') }}" class="w-full bg-abyss hover:bg-marine text-pearl py-2 rounded-lg text-xs font-bold transition opacity-90 hover:opacity-100">
                                        Beli Langsung
                                    </button>
                                </div>
                            </form>
                        @else
                            {{-- ================================================================= --}}
                            {{-- Produk Kg TIDAK punya tombol Add to Cart langsung di kartu katalog — --}}
                            {{-- harga & stok berbeda per varian, jadi user WAJIB ke Halaman Detail   --}}
                            {{-- Produk dulu untuk memilih potongan/gramasi sebelum bisa checkout.    --}}
                            {{-- ================================================================= --}}
                            <a href="{{ route('produk.show', $product->slug) }}"
                               class="mt-auto w-full inline-flex items-center justify-center gap-2 bg-gold hover:brightness-110 text-abyss py-3 rounded-xl text-sm font-bold transition shadow-glow">
                                ⚖️ Pilih Berat / Varian
                            </a>
                        @endif
                    </div>
                </div>
                @empty
                    <div class="col-span-full py-20 text-center bg-white/90 rounded-3xl border-2 border-dashed border-ink/10">
                        <p class="text-ink/40 italic">Produk tidak ditemukan...</p>
                    </div>
                @endforelse

            </div>
        </div>
    </div>
</main>

@endsection
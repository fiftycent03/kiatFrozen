@extends('layouts.app')

@section('title', $product->name . ' | KIAT Frozen Food')

@section('content')

{{-- Konten disesuaikan dengan Tema Abyss/Pearl/Gold, konsisten dengan katalog.blade.php --}}
{{-- Alpine.js dipakai untuk reaktivitas: galeri foto (klik thumbnail) + logika       --}}
{{-- kondisional Pcs/Kg (pilih varian dulu sebelum Add to Cart aktif).                --}}
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>[x-cloak] { display: none !important; }</style>

<main class="max-w-6xl mx-auto py-10 px-4 sm:px-6 lg:px-8 font-sans">

    <a href="{{ route('produk.kategori') }}" class="text-sm font-medium text-lagoon hover:text-gold transition">
        &larr; Kembali ke Katalog
    </a>

    <div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-10"
         {{-- `variants` disuntik dari DB (kosong utk produk Pcs). `selectedVariant` null
              di awal — inilah KUNCI logika "wajib pilih varian dulu" untuk produk Kg. --}}
         x-data="{
            {{-- PENTING: pakai Js::from(), BUKAN @json(), untuk data yang disuntik ke
                 dalam atribut HTML x-data="...". @json() adalah json_encode() polos —
                 setiap tanda kutip ganda (") di dalam JSON-nya (selalu ada, karena key/
                 string JSON dibungkus ") akan MENUTUP atribut x-data lebih awal, membuat
                 sisa kode Alpine bocor jadi teks HTML biasa. Js::from() meng-encode "
                 menjadi " sehingga aman disisipkan di dalam atribut yang dibungkus
                 tanda kutip ganda. --}}
            images: {{ Illuminate\Support\Js::from($product->images->pluck('path')->map(fn($p) => asset('storage/'.$p))) }},
            active: 0,
            unitType: '{{ $product->unit_type }}',
            {{-- Field stok dihapus atas permintaan --}}
            variants: {{ Illuminate\Support\Js::from($product->variants->map(fn($v) => ['id' => $v->id, 'label' => $v->label, 'price' => (float) $v->price])) }},
            selectedVariant: null,
            qty: {{ (int) ($product->min_pembelian ?? 1) }},
            get variant() { return this.variants.find(v => v.id === this.selectedVariant) || null; },
            get currentPrice() { return this.unitType === 'kg' ? (this.variant ? this.variant.price : null) : {{ (float) $product->price_per_kg }}; },
            selectVariant(v) { this.selectedVariant = v.id; this.qty = 1; },
            incQty() { this.qty++; },
            decQty() { const min = this.unitType === 'kg' ? 1 : {{ (int) ($product->min_pembelian ?? 1) }}; if (this.qty > min) this.qty--; },
            formatRp(n) { return n === null ? '—' : 'Rp' + Number(n).toLocaleString('id-ID'); },
         }">

        {{-- ================================================================= --}}
        {{-- GALERI FOTO: 1 gambar utama di atas + maksimal 4 thumbnail.        --}}
        {{-- Klik thumbnail -> ganti gambar utama (state `active`, murni Alpine, --}}
        {{-- tidak perlu reload halaman).                                       --}}
        {{-- ================================================================= --}}
        <div>
            <div class="aspect-square w-full overflow-hidden rounded-3xl border border-ink/5 bg-white/90 shadow-sm">
                <template x-if="images.length > 0">
                    {{-- Js::from() dipakai lagi di sini (bukan addslashes) — addslashes hanya
                         aman untuk string JS, TIDAK melindungi atribut HTML itu sendiri bila
                         nama produk mengandung tanda kutip ganda ("). --}}
                    <img :src="images[active]" :alt="{{ Illuminate\Support\Js::from($product->name) }}" class="h-full w-full object-cover">
                </template>
                <template x-if="images.length === 0">
                    <div class="h-full w-full flex items-center justify-center text-ink/30 text-sm italic">Belum ada foto</div>
                </template>
            </div>

            <template x-if="images.length > 1">
                <div class="mt-4 grid grid-cols-4 gap-3">
                    {{-- Ditampilkan maksimal 4 thumbnail sesuai batas galeri (total foto produk sendiri sudah dibatasi 5 di Admin). --}}
                    <template x-for="(img, idx) in images.slice(0, 4)" :key="idx">
                        <button type="button" @click="active = idx"
                            class="aspect-square overflow-hidden rounded-xl border-2 transition"
                            :class="active === idx ? 'border-gold' : 'border-transparent hover:border-lagoon/40'">
                            <img :src="img" class="h-full w-full object-cover">
                        </button>
                    </template>
                </div>
            </template>
        </div>

        {{-- ================================================================= --}}
        {{-- INFO PRODUK + LOGIKA KONDISIONAL PCS / KG                          --}}
        {{-- ================================================================= --}}
        <div>
            <p class="font-mono text-xs font-semibold uppercase tracking-[0.2em] text-lagoon">
                {{ $product->category->name ?? 'KIAT Seafood' }}
            </p>
            <h1 class="mt-2 font-display text-3xl font-semibold text-ink">{{ $product->name }}</h1>

            {{-- Harga: reaktif — untuk Kg mengikuti varian yang diklik, untuk Pcs statis. --}}
            <p class="mt-4 font-mono text-3xl font-black text-ink" x-text="formatRp(currentPrice)"></p>

            @if($product->description)
                <p class="mt-4 text-ink/60 leading-relaxed">{{ $product->description }}</p>
            @endif

            {{-- ============================================================= --}}
            {{-- CABANG "PCS": harga & stok utama langsung tampil + tombol      --}}
            {{-- Add to Cart AKTIF SEKETIKA (tidak perlu memilih apa pun dulu). --}}
            {{-- ============================================================= --}}
            @if($product->isPcs())
                {{-- Field stok dihapus atas permintaan --}}
                <div class="mt-2 text-sm text-ink/50">
                    Minimal beli {{ $product->min_pembelian }} {{ $product->satuan }}
                </div>

                <form action="{{ route('cart.add') }}" method="POST" class="mt-8">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="qty" :value="qty">

                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-3 rounded-full border border-ink/10 bg-pearl px-2 py-1.5">
                            <button type="button" @click="decQty()" class="grid h-9 w-9 place-items-center rounded-full bg-white text-ink/60 hover:text-ink transition">−</button>
                            <span class="w-8 text-center font-mono font-semibold" x-text="qty"></span>
                            <button type="button" @click="incQty()" class="grid h-9 w-9 place-items-center rounded-full bg-white text-ink/60 hover:text-ink transition">+</button>
                        </div>

                        <button type="submit"
                            class="btn-shine flex-1 rounded-full bg-gold py-3.5 font-semibold text-abyss shadow-glow transition-transform hover:scale-[1.02] active:scale-[0.98] disabled:opacity-40 disabled:pointer-events-none">
                            <span class="shine"></span>
                            Tambah ke Keranjang
                        </button>
                    </div>
                </form>

            @else
                {{-- ========================================================= --}}
                {{-- CABANG "KG": WAJIB pilih Varian Potongan/Gramasi dulu.      --}}
                {{-- Tombol Add to Cart :disabled="!selectedVariant" — baru      --}}
                {{-- aktif setelah salah satu tombol varian diklik. Harga & stok --}}
                {{-- yang ditampilkan otomatis ikut berubah sesuai varian aktif. --}}
                {{-- ========================================================= --}}
                <div class="mt-6">
                    <p class="text-sm font-semibold text-ink/70 mb-3">Pilih Potongan / Gramasi:</p>

                    @if($product->variants->isEmpty())
                        <p class="text-sm text-coral">Varian belum tersedia untuk produk ini. Silakan hubungi Admin.</p>
                    @else
                        {{-- Field stok dihapus atas permintaan --}}
                        <div class="flex flex-wrap gap-2.5">
                            <template x-for="v in variants" :key="v.id">
                                <button type="button" @click="selectVariant(v)"
                                    class="rounded-xl border-2 px-4 py-2.5 text-sm font-semibold transition"
                                    :class="selectedVariant === v.id ? 'border-gold bg-gold/10 text-ink' : 'border-ink/10 text-ink/70 hover:border-lagoon/40'">
                                    <span x-text="v.label"></span>
                                    <span class="block font-mono text-xs font-normal text-ink/50" x-text="formatRp(v.price)"></span>
                                </button>
                            </template>
                        </div>

                        <p class="mt-3 text-xs text-ink/40">
                            <template x-if="!variant">Klik salah satu varian di atas untuk melanjutkan.</template>
                        </p>
                    @endif
                </div>

                <form action="{{ route('cart.add') }}" method="POST" class="mt-8">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    {{-- variant_id kosong (belum dipilih) -> CartController@add akan menolak,
                         tapi tombol submit sudah di-disable duluan lewat :disabled di bawah. --}}
                    <input type="hidden" name="variant_id" :value="selectedVariant">
                    <input type="hidden" name="qty" :value="qty">

                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-3 rounded-full border border-ink/10 bg-pearl px-2 py-1.5">
                            <button type="button" @click="decQty()" :disabled="!selectedVariant" class="grid h-9 w-9 place-items-center rounded-full bg-white text-ink/60 hover:text-ink transition disabled:opacity-30">−</button>
                            <span class="w-8 text-center font-mono font-semibold" x-text="qty"></span>
                            <button type="button" @click="incQty()" :disabled="!selectedVariant" class="grid h-9 w-9 place-items-center rounded-full bg-white text-ink/60 hover:text-ink transition disabled:opacity-30">+</button>
                        </div>

                        {{-- INTI ATURAN: tombol nonaktif selama `selectedVariant` masih null. --}}
                        <button type="submit" :disabled="!selectedVariant"
                            class="btn-shine flex-1 rounded-full bg-gold py-3.5 font-semibold text-abyss shadow-glow transition-transform hover:scale-[1.02] active:scale-[0.98] disabled:opacity-40 disabled:pointer-events-none">
                            <span class="shine"></span>
                            <span x-text="selectedVariant ? 'Tambah ke Keranjang' : 'Pilih Varian Dulu'"></span>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</main>

{{-- Floating WA Button --}}
@php
    // Nomor tujuan CS (format internasional tanpa '+' sesuai spek wa.me).
    $waNumber = '6281332683338';

    // Pesan template: nama produk & URL halaman ini disuntik dinamis.
    // url()->current() otomatis mengambil URL halaman detail produk yang
    // sedang dibuka pengunjung (mis. /produk/detail/salmon-fillet).
    $waMessage = "Halo Admin Kiat Surabaya, saya ingin bertanya tentang produk *{$product->name}* ini: " . url()->current();

    // urlencode() WAJIB dipakai di sini — bukan sekadar {{ }} biasa — karena
    // pesan ini menjadi VALUE query string (?text=...). Tanpa urlencode(),
    // spasi/tanda bintang/karakter simbol lain akan merusak parsing URL oleh
    // browser sebelum sempat diteruskan ke aplikasi WhatsApp.
    $waLink = 'https://wa.me/' . $waNumber . '?text=' . urlencode($waMessage);
@endphp
<a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
   title="Chat Customer Service via WhatsApp"
   class="fixed bottom-6 right-6 z-50 grid h-14 w-14 place-items-center rounded-full bg-[#25D366] text-white shadow-lg shadow-black/25 transition-transform duration-300 hover:scale-110">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="h-7 w-7" fill="currentColor">
        <path d="M16.004 3C9.377 3 4 8.373 4 15c0 2.386.7 4.61 1.912 6.48L4 29l7.72-1.876A11.94 11.94 0 0 0 16.004 27C22.63 27 28 21.627 28 15S22.63 3 16.004 3Zm0 21.75c-1.94 0-3.75-.53-5.31-1.45l-.38-.225-4.58 1.113 1.14-4.463-.246-.386A9.71 9.71 0 0 1 5.25 15c0-5.936 4.818-10.75 10.754-10.75S26.75 9.064 26.75 15 21.94 24.75 16.004 24.75Zm5.79-8.07c-.317-.159-1.877-.927-2.168-1.033-.29-.106-.502-.159-.714.159-.211.317-.818 1.033-1.003 1.245-.185.212-.37.238-.687.08-.317-.159-1.338-.494-2.548-1.575-.942-.84-1.578-1.878-1.763-2.196-.185-.317-.02-.489.139-.647.143-.142.317-.37.476-.556.159-.185.211-.317.317-.529.106-.212.053-.397-.026-.556-.08-.159-.714-1.723-.978-2.36-.257-.618-.519-.534-.714-.544-.185-.009-.397-.011-.608-.011-.212 0-.556.08-.847.397-.29.317-1.11 1.084-1.11 2.645 0 1.56 1.137 3.068 1.296 3.28.159.212 2.238 3.419 5.423 4.795.758.327 1.35.523 1.811.669.761.242 1.453.208 2.001.126.61-.091 1.877-.767 2.142-1.508.264-.741.264-1.376.185-1.508-.079-.132-.29-.212-.608-.37Z"/>
    </svg>
</a>

@endsection

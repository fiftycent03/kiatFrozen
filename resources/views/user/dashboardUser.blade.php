@extends('layouts.app')

@section('title', 'Beranda | KIAT Frozen Food')

@section('content')

{{-- HERO SECTION --}}
<div class="bg-blue-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 flex flex-col md:flex-row items-center">
        
        {{-- Teks Kiri --}}
        <div class="md:w-1/2 text-white text-center md:text-left mb-8 md:mb-0">
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-4 leading-tight">
                Seafood Segar, <br>Langsung ke Dapurmu!
            </h1>
            <p class="text-blue-100 text-lg mb-8 max-w-lg mx-auto md:mx-0">
                Kualitas restoran bintang lima kini bisa dinikmati di rumah. Cumi, Dory, Udang, dan lainnya. Higienis & Halal.
            </p>
            <a href="{{ route('produk.kategori') }}" class="inline-block bg-white text-blue-700 font-bold py-3 px-8 rounded-full shadow-lg hover:bg-gray-100 transition transform hover:-translate-y-1">
                Mulai Belanja 🛒
            </a>
        </div>

        {{-- Gambar Hero --}}
        <div class="md:w-1/2">
            <img src="https://images.unsplash.com/photo-1565680018434-b513d5e5fd47?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" 
                 alt="Frozen Food" 
                 class="rounded-2xl shadow-2xl transform md:rotate-3 hover:rotate-0 transition duration-500">
        </div>

    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- ============================================================= --}}
    {{-- CAROUSEL KATEGORI — geser horizontal, 4 card tampil sekaligus di desktop --}}
    {{-- Blok "Beberapa Produk Kami" sengaja DIHAPUS sesuai permintaan; --}}
    {{-- dashboard kini hanya menampilkan kategori. --}}
    {{-- ============================================================= --}}
    <div class="mb-16">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Jelajahi Kategori</h2>

            {{-- Tombol navigasi (kiri/kanan) — hanya tampil di desktop, mobile pakai swipe jari --}}
            <div class="hidden md:flex gap-2">
                <button id="cat-prev" type="button" aria-label="Sebelumnya"
                        class="w-10 h-10 rounded-full bg-white border border-gray-200 shadow-sm flex items-center justify-center text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition">
                    &#8249;
                </button>
                <button id="cat-next" type="button" aria-label="Berikutnya"
                        class="w-10 h-10 rounded-full bg-white border border-gray-200 shadow-sm flex items-center justify-center text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition">
                    &#8250;
                </button>
            </div>
        </div>

        {{-- Track carousel: flex + overflow-x-auto + scroll-snap. "no-scrollbar" menyembunyikan --}}
        {{-- scrollbar bawaan browser (definisi CSS-nya ada di <style> di bawah). --}}
        <div id="category-carousel" class="flex overflow-x-auto snap-x snap-mandatory gap-4 pb-4 no-scrollbar scroll-smooth">
            @forelse($categories as $cat)
                {{--
                    Card kategori — STRUKTUR BERSIH (memperbaiki bug teks ganda):
                    1) Div ini sendiri = layer background-image (gambar kategori hasil upload Admin).
                    2) Satu div overlay hitam transparan (bg-black bg-opacity-40).
                    3) SATU h3 judul putih di tengah — pakai `text-shadow` CSS biasa (inline style),
                       BUKAN utility `drop-shadow-*` Tailwind. Filter drop-shadow Tailwind yang
                       dipadukan dengan `transform`/`scale` saat hover itulah biang bug "teks ganda"
                       (shadow-nya ikut di-render seperti salinan teks terpisah di beberapa browser).
                    Lebar card diatur agar tepat 4 buah terlihat sekaligus di layar desktop (md+).
                --}}
                <a href="/produk/{{ $cat->slug }}"
                   class="group relative flex-shrink-0 snap-center min-w-[calc(50%-0.5rem)] sm:min-w-[calc(33.333%-0.7rem)] md:min-w-[calc(25%-0.75rem)] h-56 md:h-72 rounded-2xl overflow-hidden shadow-md"
                   style="background-image: url('{{ $cat->image ? asset('storage/' . $cat->image) : 'https://placehold.co/400x500?text=' . urlencode($cat->name) }}'); background-size: cover; background-position: center;">

                    {{-- Overlay gelap transparan agar judul tetap terbaca di atas gambar. --}}
                    <div class="absolute inset-0 bg-black bg-opacity-40 group-hover:bg-opacity-30 transition duration-300"></div>

                    {{-- Judul kategori — HANYA SATU elemen teks, tidak ada teks bayangan duplikat. --}}
                    <div class="absolute inset-0 flex items-center justify-center">
                        <h3 class="text-white font-bold text-2xl md:text-3xl tracking-wide text-center px-3 transition-transform duration-300 group-hover:scale-110"
                            style="text-shadow: 0 2px 6px rgba(0,0,0,0.6);">
                            {{ $cat->name }}
                        </h3>
                    </div>
                </a>
            @empty
                {{-- Fallback bila Admin belum membuat kategori apa pun. --}}
                <div class="w-full text-center py-16 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
                    <div class="text-4xl mb-2">🐟</div>
                    <p class="text-gray-500 font-medium italic">Belum ada kategori yang ditampilkan.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

<style>
    /* Sembunyikan scrollbar bawaan browser pada track carousel kategori (fungsi scroll tetap jalan). */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<script>
    // Navigasi carousel kategori via tombol kiri/kanan — vanilla JS (tanpa dependensi tambahan).
    (function () {
        const track   = document.getElementById('category-carousel');
        const prevBtn = document.getElementById('cat-prev');
        const nextBtn = document.getElementById('cat-next');
        if (!track || !prevBtn || !nextBtn) return;

        // Hitung lebar 1 card (+ gap-4 = 16px) agar geseran pas satu kartu per klik.
        function cardScrollStep() {
            const firstCard = track.querySelector('a');
            return firstCard ? firstCard.offsetWidth + 16 : 300;
        }

        prevBtn.addEventListener('click', () => track.scrollBy({ left: -cardScrollStep(), behavior: 'smooth' }));
        nextBtn.addEventListener('click', () => track.scrollBy({ left: cardScrollStep(), behavior: 'smooth' }));
    })();
</script>

@endsection
<header class="bg-white shadow-sm sticky top-0 z-50 font-sans">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">

            {{-- 1. LOGO (KIRI) --}}
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ route('user.dashboard') }}" class="flex items-center gap-2 group">
                    <span class="text-3xl">❄️</span>
                    <div class="flex flex-col">
                        <span class="text-xl font-extrabold text-blue-600 tracking-tight group-hover:text-blue-700 transition">KIAT FROZEN</span>
                        <span class="text-[10px] text-gray-500 font-semibold tracking-wider uppercase -mt-1">Fresh & High Quality</span>
                    </div>
                </a>
            </div>

            {{-- 2. NAVIGASI (TENGAH) --}}
            <nav class="hidden md:flex items-center space-x-8">
                <a href="{{ route('user.dashboard') }}" 
                   class="text-sm font-medium transition {{ request()->routeIs('user.dashboard') ? 'text-blue-600 font-bold' : 'text-gray-500 hover:text-blue-600' }}">
                   Beranda
                </a>

                <a href="{{ route('tentang.kami') }}" 
                   class="text-sm font-medium transition {{ request()->routeIs('tentang.kami') ? 'text-blue-600 font-bold' : 'text-gray-500 hover:text-blue-600' }}">
                   Tentang Kami
                </a>

                <div class="relative group">
                    <button id="katalogBtn" class="flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 focus:outline-none transition group-hover:text-blue-600">
                        <span>Katalog Produk</span>
                        <svg class="ml-1 h-4 w-4 transition-transform group-hover:rotate-180" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div id="katalogMenu" class="absolute left-1/2 transform -translate-x-1/2 mt-2 w-56 rounded-xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden transition-all duration-200 z-50 overflow-hidden">
                        <div class="py-2">
                            <a href="{{ route('produk.kategori') }}" class="block px-4 py-2.5 text-sm font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 transition border-b border-gray-100">
                                📂 Semua Kategori
                            </a>
                            {{--
                                Daftar kategori diambil LANGSUNG dari tabel categories (dinamis,
                                sesuai data Admin) — bukan lagi array hardcoded, sehingga kategori
                                baru (mis. "Japanes") otomatis muncul di sini juga.
                                Variabel diberi nama $navKategori (bukan $kategori) agar TIDAK menimpa
                                variabel $kategori dari KatalogController saat partial ini dirender
                                di halaman /produk/{kategori} (partial berbagi scope dgn parent view).
                            --}}
                            @foreach(\App\Models\Category::where('is_active', 1)->orderBy('name')->get() as $navKategori)
                                <a href="/produk/{{ $navKategori->slug }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition border-b border-gray-50 last:border-0">
                                   {{ $navKategori->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </nav>

            {{-- 3. USER ACTIONS (KANAN) --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('cart.index') }}" class="relative p-2.5 rounded-full text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition group" title="Keranjang Belanja">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 5c.17.678-.369 1.493-1.032 1.493H4.212c-.663 0-1.202-.815-1.032-1.492l1.263-5c.07-.278.263-.499.529-.604a14.459 14.459 0 014.373-.749 14.459 14.459 0 014.373.749c.266.105.459.326.529.604z" />
                    </svg>
                    
                    {{-- PERBAIKAN DI SINI: Menggunakan count() untuk menghitung JENIS produk --}}
                    @php $cartItemCount = count(session('cart', [])); @endphp
                    
                    @if($cartItemCount > 0)
                        <span id="cart-badge" class="absolute top-1 right-1 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold leading-none text-white bg-red-500 rounded-full border-2 border-white shadow-sm">
                            {{ $cartItemCount }}
                        </span>
                    @endif
                </a>

                <div class="h-6 w-px bg-gray-200 mx-1 hidden md:block"></div>

                @auth
                    {{-- Tampilan saat Login --}}
                    <a href="{{ route('user.riwayat') }}" class="hidden md:flex items-center gap-2 px-4 py-2 bg-white border border-blue-200 text-blue-600 rounded-full hover:bg-blue-50 transition text-sm font-bold shadow-sm">
                        <span>📦</span> <span>Pesanan</span>
                    </a>
                    <div class="relative ml-2">
                        <button type="button" id="userMenuBtn" class="flex items-center gap-2 focus:outline-none group">
                            <div class="h-9 w-9 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold ring-2 ring-white shadow-md">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="hidden md:block text-left">
                                <div class="text-[10px] text-gray-400 uppercase font-bold leading-none mb-1">Halo,</div>
                                <div class="text-sm font-bold text-gray-700 leading-none group-hover:text-blue-600 transition">{{ Auth::user()->name }}</div>
                            </div>
                        </button>
                        {{-- Dropdown User --}}
                        <div id="userMenu" class="absolute right-0 mt-3 w-56 rounded-2xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 hidden z-50 overflow-hidden">
                            <div class="py-2">
                                <a href="{{ route('user.riwayat') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 transition">Riwayat Pesanan</a>
                                <a href="{{ route('user.address.index') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 transition">Alamat Saya</a>
                                <form action="{{ route('logout') }}" method="GET">
                                    <button type="submit" class="w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 font-bold transition">Keluar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Tombol Login/Daftar --}}
                    <div class="flex items-center gap-2">
                        <a href="{{ route('login') }}" class="text-gray-500 hover:text-blue-600 font-bold text-sm px-3 py-2 transition">Masuk</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-5 py-2.5 rounded-full text-sm font-bold hover:bg-blue-700 transition shadow-md">Daftar</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function () {
    function setupDropdown(btnId, menuId) {
        const btn = document.getElementById(btnId);
        const menu = document.getElementById(menuId);
        if (!btn || !menu) return;
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isHidden = menu.classList.contains('hidden');
            document.querySelectorAll('[id$="Menu"]').forEach(el => el.classList.add('hidden'));
            if(isHidden) menu.classList.remove('hidden');
        });
    }
    setupDropdown('katalogBtn', 'katalogMenu');
    setupDropdown('userMenuBtn', 'userMenu');
    document.addEventListener('click', () => {
        document.querySelectorAll('[id$="Menu"]').forEach(m => m.classList.add('hidden'));
    });
});
</script>
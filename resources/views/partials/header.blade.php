{{-- ============================================================= --}}
{{-- NAVBAR — Konten disesuaikan dengan Tema Abyss/Pearl/Gold        --}}
{{-- Desain visual diadopsi dari user/dashboardUser.blade.php,       --}}
{{-- namun SELURUH tautan rute & logika auth (dropdown katalog       --}}
{{-- dinamis, badge keranjang, menu user) dipertahankan agar         --}}
{{-- navigasi tetap berfungsi di semua halaman.                      --}}
{{-- ============================================================= --}}
<header id="siteNav" class="sticky inset-x-0 top-0 z-50 border-b border-white/5 bg-abyss/95 backdrop-blur-md">
    <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-5 sm:px-8">

        {{-- LOGO (KIRI) --}}
        <a href="{{ route('user.dashboard') }}" class="flex items-center gap-3">
            <img src="{{ asset('storage/Logo_Kiat.png') }}" alt="Logo Karya Inti Alam Tunggal"
                class="h-11 w-11 rounded-full object-cover shadow-md border border-white/20" />
            <span class="flex flex-col leading-none">
                <span class="font-display text-lg font-semibold tracking-tight text-pearl">KIAT SURABAYA</span>
                <span class="mt-1 font-mono text-[9px] uppercase tracking-[0.18em] text-pearl/50">Karya Inti Alam Tunggal</span>
            </span>
        </a>

        {{-- NAVIGASI (TENGAH) --}}
        <nav class="hidden items-center gap-9 md:flex">
            <a href="{{ route('user.dashboard') }}"
               class="text-sm font-medium transition-colors hover:text-gold {{ request()->routeIs('user.dashboard') || request()->routeIs('home') ? 'text-gold' : 'text-pearl/70' }}">
               Beranda
            </a>
            <a href="{{ route('tentang.kami') }}"
               class="text-sm font-medium transition-colors hover:text-gold {{ request()->routeIs('tentang.kami') ? 'text-gold' : 'text-pearl/70' }}">
               Tentang Kami
            </a>

            {{-- Dropdown Katalog: kategori tetap diambil dinamis dari tabel categories --}}
            <div class="relative group">
                <button id="katalogBtn" class="flex items-center gap-1 text-sm font-medium transition-colors hover:text-gold {{ request()->routeIs('produk.kategori') ? 'text-gold' : 'text-pearl/70' }}">
                    <span>Katalog Produk</span>
                    <i data-lucide="chevron-down" class="h-4 w-4"></i>
                </button>

                <div id="katalogMenu" class="absolute left-1/2 mt-3 hidden w-56 -translate-x-1/2 overflow-hidden rounded-2xl border border-ink/5 bg-pearl shadow-xl ring-1 ring-black/5 z-50">
                    <div class="py-2">
                        <a href="{{ route('produk.kategori') }}" class="block border-b border-ink/5 bg-gold/10 px-4 py-2.5 text-sm font-bold text-lagoon transition hover:bg-gold/20">
                            Semua Kategori
                        </a>
                        {{--
                            Kategori diambil LANGSUNG dari tabel categories (dinamis, sesuai data Admin).
                            Variabel $navKategori dipakai agar TIDAK menimpa $kategori dari KatalogController
                            saat partial ini dirender di halaman /produk/{kategori}.
                        --}}
                        @foreach(\App\Models\Category::where('is_active', 1)->orderBy('name')->get() as $navKategori)
                            <a href="/produk/{{ $navKategori->slug }}" class="block border-b border-ink/5 px-4 py-2.5 text-sm text-ink/70 transition last:border-0 hover:bg-lagoon/5 hover:text-lagoon">
                               {{ $navKategori->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </nav>

        {{-- USER ACTIONS (KANAN) --}}
        <div class="flex items-center gap-1.5 sm:gap-2">

            {{-- Keranjang + badge jumlah item (dari session) --}}
            <a href="{{ route('cart.index') }}" class="relative grid h-10 w-10 place-items-center rounded-full text-pearl/80 transition hover:bg-white/5 hover:text-gold" title="Keranjang Belanja">
                <i data-lucide="shopping-cart" class="h-5 w-5"></i>
                @php $cartItemCount = count(session('cart', [])); @endphp
                @if($cartItemCount > 0)
                    <span id="cart-badge" class="absolute -right-0.5 -top-0.5 grid h-[18px] w-[18px] min-w-[18px] place-items-center rounded-full bg-gold px-1 font-mono text-[10px] font-bold text-abyss">
                        {{ $cartItemCount }}
                    </span>
                @endif
            </a>

            @auth
                {{-- Tampilan saat Login: pintasan Pesanan + menu akun --}}
                <a href="{{ route('user.riwayat') }}" class="hidden items-center gap-2 rounded-full px-3 py-2 text-pearl/80 transition hover:bg-white/5 hover:text-gold sm:flex" title="Pesanan Saya">
                    <i data-lucide="package" class="h-5 w-5"></i>
                    <span class="text-xs font-medium">Pesanan</span>
                </a>

                <div class="relative">
                    <button type="button" id="userMenuBtn" class="flex items-center gap-2 rounded-full p-0.5 transition hover:bg-white/5">
                        <span class="grid h-9 w-9 place-items-center rounded-full bg-gold text-sm font-bold text-abyss shadow-md ring-2 ring-white/10">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </span>
                        <span class="hidden pr-1 text-left md:block">
                            <span class="block font-mono text-[9px] uppercase tracking-wider text-pearl/40 leading-none">Halo,</span>
                            <span class="block text-sm font-semibold text-pearl leading-tight">{{ \Illuminate\Support\Str::limit(Auth::user()->name, 10) }}</span>
                        </span>
                    </button>

                    {{-- Dropdown User --}}
                    <div id="userMenu" class="absolute right-0 mt-3 hidden w-56 overflow-hidden rounded-2xl border border-ink/5 bg-pearl shadow-xl ring-1 ring-black/5 z-50">
                        <div class="py-2">
                            <a href="{{ route('user.riwayat') }}" class="block px-4 py-3 text-sm text-ink/70 transition hover:bg-lagoon/5 hover:text-lagoon">Riwayat Pesanan</a>
                            <a href="{{ route('user.address.index') }}" class="block px-4 py-3 text-sm text-ink/70 transition hover:bg-lagoon/5 hover:text-lagoon">Alamat Saya</a>
                            <form action="{{ route('logout') }}" method="GET" class="border-t border-ink/5">
                                <button type="submit" class="w-full px-4 py-3 text-left text-sm font-bold text-coral transition hover:bg-coral/5">Keluar</button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                {{-- Tombol Login/Daftar --}}
                <a href="{{ route('login') }}" class="hidden px-3 py-2 text-sm font-medium text-pearl/80 transition hover:text-gold sm:block">Masuk</a>
                <a href="{{ route('register') }}" class="rounded-full bg-gold px-5 py-2.5 text-sm font-semibold text-abyss shadow-glow transition-transform hover:scale-[1.04] active:scale-[0.97]">Daftar</a>
            @endauth

            {{-- Toggle menu mobile --}}
            <button id="menuToggle" class="grid h-10 w-10 place-items-center rounded-full text-pearl/80 transition hover:bg-white/5 hover:text-gold md:hidden">
                <i data-lucide="menu" id="menuIconOpen" class="h-5 w-5"></i>
                <i data-lucide="x" id="menuIconClose" class="hidden h-5 w-5"></i>
            </button>
        </div>
    </div>

    {{-- Panel navigasi mobile --}}
    <div id="mobilePanel" class="hidden flex-col gap-1 border-t border-white/5 bg-abyss px-5 pb-5 pt-2 md:hidden">
        <a href="{{ route('user.dashboard') }}" class="rounded-xl px-4 py-3 text-sm font-medium text-pearl/80 transition hover:bg-white/5 hover:text-gold">Beranda</a>
        <a href="{{ route('tentang.kami') }}" class="rounded-xl px-4 py-3 text-sm font-medium text-pearl/80 transition hover:bg-white/5 hover:text-gold">Tentang Kami</a>
        <a href="{{ route('produk.kategori') }}" class="rounded-xl px-4 py-3 text-sm font-medium text-pearl/80 transition hover:bg-white/5 hover:text-gold">Katalog Produk</a>
        <a href="{{ route('cart.index') }}" class="rounded-xl px-4 py-3 text-sm font-medium text-pearl/80 transition hover:bg-white/5 hover:text-gold">Keranjang</a>
        @auth
            <a href="{{ route('user.riwayat') }}" class="rounded-xl px-4 py-3 text-sm font-medium text-pearl/80 transition hover:bg-white/5 hover:text-gold">Pesanan Saya</a>
            <a href="{{ route('user.address.index') }}" class="rounded-xl px-4 py-3 text-sm font-medium text-pearl/80 transition hover:bg-white/5 hover:text-gold">Alamat Saya</a>
            <form action="{{ route('logout') }}" method="GET">
                <button type="submit" class="w-full rounded-xl px-4 py-3 text-left text-sm font-bold text-coral transition hover:bg-coral/10">Keluar</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="rounded-xl px-4 py-3 text-sm font-medium text-pearl/80 transition hover:bg-white/5 hover:text-gold">Masuk</a>
            <a href="{{ route('register') }}" class="mt-1 rounded-full bg-gold px-4 py-3 text-center text-sm font-semibold text-abyss">Daftar</a>
        @endauth
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Dropdown desktop (katalog & user) — toggle klik, tutup saat klik di luar.
    function setupDropdown(btnId, menuId) {
        const btn = document.getElementById(btnId);
        const menu = document.getElementById(menuId);
        if (!btn || !menu) return;
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isHidden = menu.classList.contains('hidden');
            document.querySelectorAll('[id$="Menu"]').forEach(el => el.classList.add('hidden'));
            if (isHidden) menu.classList.remove('hidden');
        });
    }
    setupDropdown('katalogBtn', 'katalogMenu');
    setupDropdown('userMenuBtn', 'userMenu');
    document.addEventListener('click', () => {
        document.querySelectorAll('[id$="Menu"]').forEach(m => m.classList.add('hidden'));
    });

    // Toggle panel navigasi mobile.
    const menuToggle = document.getElementById('menuToggle');
    const mobilePanel = document.getElementById('mobilePanel');
    const menuIconOpen = document.getElementById('menuIconOpen');
    const menuIconClose = document.getElementById('menuIconClose');
    if (menuToggle && mobilePanel) {
        menuToggle.addEventListener('click', () => {
            const isHidden = mobilePanel.classList.contains('hidden');
            mobilePanel.classList.toggle('hidden');
            mobilePanel.classList.toggle('flex');
            menuIconOpen.classList.toggle('hidden', isHidden);
            menuIconClose.classList.toggle('hidden', !isHidden);
        });
    }
});
</script>

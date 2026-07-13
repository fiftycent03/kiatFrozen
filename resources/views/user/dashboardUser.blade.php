<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>KIAT — Karya Inti Alam Tunggal | Seafood Premium</title>
<meta name="description" content="Seafood segar premium, higienis, dan bersertifikat Halal — langsung dari laut Nusantara ke dapurmu." />

<!-- Google Fonts: Fraunces (display), Inter (body), JetBrains Mono (utility/harga) -->
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500;1,9..144,600&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet" />

<!-- Tailwind CDN -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          abyss:  '#071726',   // deep marine navy — dasar hero
          marine: '#0B3E56',   // gradasi tengah hero
          lagoon: '#16808A',   // aksen teal UI (label, ikon)
          pearl:  '#F6F1E7',   // dasar section terang (mutiara/pasir)
          ink:    '#101B22',   // teks utama di atas pearl
          gold:   '#D4AF37',   // aksen premium (CTA, tag, divider)
          coral:  '#E2683F',   // aksen hangat, dipakai sangat terbatas
        },
        fontFamily: {
          display: ['"Fraunces"', 'serif'],
          sans: ['"Inter"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
          mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
        },
        boxShadow: {
          glow: '0 10px 30px -8px rgba(212,175,55,0.35)',
        },
      },
    },
  };
</script>

<!-- Lucide Icons -->
<script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.js"></script>

<style>
  html { scroll-behavior: smooth; }
  body { -webkit-font-smoothing: antialiased; }

  :focus-visible { outline: 2px solid #D4AF37; outline-offset: 3px; border-radius: 4px; }
  @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation-duration: 0.001ms !important; animation-iteration-count: 1 !important; transition-duration: 0.001ms !important; scroll-behavior: auto !important; } }

  .reveal { opacity: 0; transform: translateY(28px); transition: opacity .75s cubic-bezier(.22,.61,.36,1), transform .75s cubic-bezier(.22,.61,.36,1); }
  .reveal-visible { opacity: 1; transform: translateY(0); }

  .no-scrollbar::-webkit-scrollbar { display: none; }
  .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

  .catch-tag { background: #F6F1E7; border: 1.5px dashed rgba(16,27,34,0.25); border-radius: 10px; }
  .catch-tag::before { content: ''; position: absolute; left: -8px; top: 50%; transform: translateY(-50%); width: 13px; height: 13px; border-radius: 9999px; border: 2px solid rgba(16,27,34,0.25); background: transparent; }

  @keyframes floaty { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-9px); } }
  .float-slow { animation: floaty 5.5s ease-in-out infinite; }
  .float-slow-delay { animation: floaty 5.5s ease-in-out infinite; animation-delay: 1.2s; }

  @keyframes waveDrift { 0%, 100% { transform: translateX(0) translateY(0); } 50% { transform: translateX(-1.2%) translateY(2px); } }
  .wave-drift { animation: waveDrift 12s ease-in-out infinite; }

  @keyframes badgePulse { 0% { transform: scale(1); } 35% { transform: scale(1.4); } 65% { transform: scale(0.92); } 100% { transform: scale(1); } }
  .badge-pulse { animation: badgePulse .5s ease; }

  @keyframes shineSweep { 0% { transform: translateX(-140%) skewX(-18deg); } 100% { transform: translateX(260%) skewX(-18deg); } }
  .btn-shine { position: relative; overflow: hidden; }
  .btn-shine .shine { position: absolute; top: 0; left: -30%; width: 22%; height: 100%; background: rgba(255,255,255,0.4); transform: skewX(-18deg); pointer-events: none; }
  .btn-shine:hover .shine { animation: shineSweep .85s ease forwards; }

  .grain-overlay { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E"); mix-blend-mode: overlay; }
  .wave-pattern { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='110' height='34' viewBox='0 0 110 34'%3E%3Cpath d='M0 17 Q27.5 0 55 17 T110 17' stroke='%23F6F1E7' stroke-width='1' fill='none'/%3E%3C/svg%3E"); background-repeat: repeat; }

  .category-card .cat-sub { max-height: 0; opacity: 0; margin-top: 0; overflow: hidden; transition: max-height .5s ease, opacity .5s ease, margin-top .5s ease; }
  .category-card:hover .cat-sub, .category-card:focus-within .cat-sub { max-height: 40px; opacity: 1; margin-top: 0.5rem; }
  .tilt-perspective { perspective: 1200px; }
</style>
</head>

<body class="bg-pearl font-sans text-ink antialiased">

<!-- =========================================================
     NAVBAR
========================================================== -->
<header id="siteNav" class="fixed inset-x-0 top-0 z-50 border-b border-white/5 bg-abyss/70 backdrop-blur-md transition-colors duration-300">
  <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-5 sm:px-8">
    <a href="#" class="flex items-center gap-3">
    <img src="{{ asset('storage/Logo_Kiat.png') }}" alt="Logo Karya Inti Alam Tunggal" 
      class="h-11 w-11 rounded-full object-cover shadow-md border border-white/20" />
      <span class="flex flex-col leading-none">
        <span class="font-display text-lg font-semibold tracking-tight text-pearl">KIAT</span>
        <span class="mt-1 font-mono text-[9px] uppercase tracking-[0.18em] text-pearl/50">Karya Inti Alam Tunggal</span>
      </span>
    </a>

    <nav class="hidden items-center gap-9 md:flex">
      <a href="#" class="text-sm font-medium text-pearl transition-colors hover:text-gold">Beranda</a>
      <a href="{{ route('tentang.kami') }}" class="text-sm font-medium text-pearl/70 transition-colors hover:text-gold">Tentang
        Kami</a>
      <a href="{{ route('produk.kategori') }}"
        class="text-sm font-medium text-pearl/70 transition-colors hover:text-gold">Katalog Produk</a>
      <!-- <a href="#promo" class="relative flex items-center text-sm font-medium text-pearl/70 transition-colors hover:text-gold">
        Promo -->
        <!-- <span class="relative ml-1.5 inline-flex h-2 w-2">
          <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-coral/70"></span>
          <span class="relative inline-flex h-2 w-2 rounded-full bg-coral"></span>
        </span> -->
      </a>
    </nav>

    <div class="flex items-center gap-1.5 sm:gap-2">
      <button class="hidden items-center gap-2 rounded-full px-3 py-2 text-pearl/80 transition hover:bg-white/5 hover:text-gold sm:flex" title="Pesanan Saya">
        <i data-lucide="package" class="h-5 w-5"></i>
        <span class="text-xs font-medium">Pesanan</span>
      </button>
      <a href="{{ route('login') }}"
        class="grid h-10 w-10 place-items-center rounded-full text-pearl/80 transition hover:bg-white/5 hover:text-gold"
        title="Akun Saya">
        <i data-lucide="user" class="h-5 w-5"></i>
      </a>
      <button id="cartBtn" class="relative grid h-10 w-10 place-items-center rounded-full text-pearl/80 transition hover:bg-white/5 hover:text-gold" title="Keranjang Belanja">
        <i data-lucide="shopping-cart" class="h-5 w-5"></i>
        {{-- Badge di-seed dari jumlah item keranjang session (server-side), lalu di-update oleh JS. --}}
        <span id="cartBadge" class="absolute -right-0.5 -top-0.5 grid h-[18px] w-[18px] min-w-[18px] place-items-center rounded-full bg-gold px-1 font-mono text-[10px] font-bold text-abyss">{{ count(session('cart', [])) }}</span>
      </button>
      <button id="menuToggle" class="grid h-10 w-10 place-items-center rounded-full text-pearl/80 transition hover:bg-white/5 hover:text-gold md:hidden">
        <i data-lucide="menu" id="menuIconOpen" class="h-5 w-5"></i>
        <i data-lucide="x" id="menuIconClose" class="hidden h-5 w-5"></i>
      </button>
    </div>
  </div>
</header>

<!-- =========================================================
     HERO SECTION
========================================================== -->
<section class="relative overflow-hidden bg-gradient-to-b from-abyss to-marine pb-28 pt-32 sm:pt-40">
  <div class="grain-overlay pointer-events-none absolute inset-0 opacity-[0.05]"></div>
  <div class="wave-pattern wave-drift pointer-events-none absolute inset-0 opacity-[0.05]"></div>
  <div class="pointer-events-none absolute left-1/2 top-0 h-[520px] w-[520px] -translate-x-1/2 rounded-full bg-lagoon/20 blur-[130px]"></div>

  <div class="relative mx-auto grid max-w-7xl grid-cols-1 items-center gap-16 px-5 sm:px-8 md:grid-cols-2 md:gap-10">
    <div class="reveal">
      <p class="mb-5 inline-flex items-center gap-2 rounded-full border border-gold/30 bg-gold/10 px-4 py-1.5 font-mono text-[11px] font-medium uppercase tracking-[0.14em] text-gold">
        Tangkapan Hari Ini · Higienis · Halal
      </p>
      <h1 class="font-display text-[2.5rem] font-semibold leading-[1.08] text-pearl sm:text-5xl lg:text-[3.7rem]">
        Seafood Segar,<br />
        <span class="italic text-gold">Langsung ke Dapurmu!</span>
      </h1>
      <p class="mt-6 max-w-md text-base leading-relaxed text-pearl/70 sm:text-lg">
        Kualitas restoran bintang lima, kini hadir di rumah. Setiap tangkapan diproses higienis, dikemas dingin, dan diantar segar dalam hitungan jam — bersertifikat
        <span class="font-medium text-pearl">Halal MUI</span>.
      </p>

      <div class="mt-9 flex flex-wrap items-center gap-6">
        <a href="{{ route('produk.kategori') }}"
          class="btn-shine inline-flex items-center gap-2 rounded-full bg-gold px-7 py-3.5 font-semibold text-abyss shadow-glow transition-transform duration-300 hover:scale-[1.04] active:scale-[0.97]">
          <span class="shine"></span>
          <span>Mulai Belanja</span>
          <i data-lucide="arrow-right" class="h-4 w-4"></i>
        </a>
        <!-- <a  href="{{ route('produk.kategori') }}" class="group inline-flex items-center gap-2 text-sm font-medium text-pearl/80 transition-colors hover:text-gold">
          Lihat Katalog Produk
          <i data-lucide="arrow-right" class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1"></i>
        </a> -->
      </div>

      <div class="mt-11 grid grid-cols-2 gap-x-6 gap-y-5 border-t border-white/10 pt-7 sm:flex sm:flex-wrap sm:gap-x-9">
        <div class="flex items-center gap-2.5"><i data-lucide="shield-check" class="h-5 w-5 flex-none text-gold"></i><span class="text-xs text-pearl/80 sm:text-sm">Higienis Terjamin</span></div>
        <div class="flex items-center gap-2.5"><i data-lucide="badge-check" class="h-5 w-5 flex-none text-gold"></i><span class="text-xs text-pearl/80 sm:text-sm">100% Halal MUI</span></div>
        <div class="flex items-center gap-2.5"><i data-lucide="clock" class="h-5 w-5 flex-none text-gold"></i><span class="text-xs text-pearl/80 sm:text-sm">Segar &lt; 24 Jam</span></div>
        <div class="flex items-center gap-2.5"><i data-lucide="truck" class="h-5 w-5 flex-none text-gold"></i><span class="text-xs text-pearl/80 sm:text-sm">Kirim Se-Indonesia</span></div>
      </div>
    </div>

    <!-- RIGHT: interactive product showcase -->
    <div class="reveal relative">
      <div class="pointer-events-none absolute -right-8 -top-8 h-72 w-72 rounded-full bg-lagoon/25 blur-3xl"></div>
      <div class="pointer-events-none absolute -bottom-10 -left-8 h-64 w-64 rounded-full bg-gold/10 blur-3xl"></div>

      <div id="heroTilt" class="tilt-perspective relative mx-auto max-w-md pb-20">
        <div id="heroCardInner" class="relative rounded-[2rem] border border-white/10 bg-white/5 p-3 shadow-2xl shadow-black/40 backdrop-blur-sm transition-transform duration-300 ease-out">
          
          <div class="overflow-hidden rounded-[1.6rem]">
            {{-- GAMBAR LOKAL: gambardepan.jpeg disuntikkan ke sini melalui Javascript array di bawah --}}
            <img id="heroImg" src="{{ asset('storage/gambardepan.jpeg') }}" alt="Produk KIAT Frozen" class="h-[360px] w-full object-cover transition-opacity duration-300 sm:h-[420px]" />
          </div>

          <div class="float-slow absolute -right-4 top-8 rounded-2xl border border-white/10 bg-abyss/80 px-3 py-2 shadow-lg backdrop-blur-md">
            <div class="flex items-center gap-1.5 text-[11px] font-medium text-pearl"><i data-lucide="badge-check" class="h-3.5 w-3.5 text-gold"></i>100% Halal</div>
          </div>
          <div class="float-slow-delay absolute -left-4 bottom-24 rounded-2xl border border-white/10 bg-abyss/80 px-3 py-2 shadow-lg backdrop-blur-md">
            <div class="flex items-center gap-1.5 text-[11px] font-medium text-pearl"><i data-lucide="clock" class="h-3.5 w-3.5 text-gold"></i>Fresh &lt; 24 Jam</div>
          </div>

          <button id="quickAddBtn" title="Tambah ke keranjang" class="absolute bottom-4 right-4 grid h-11 w-11 place-items-center rounded-full bg-gold text-abyss shadow-lg transition-transform hover:scale-110 active:scale-95">
            <i data-lucide="plus" class="h-5 w-5"></i>
          </button>

          <div id="catchTag" class="catch-tag absolute -bottom-8 left-6 w-[80%] max-w-[280px] rotate-[-3deg] px-4 py-3 shadow-xl transition-opacity duration-200">
            <div class="flex items-center justify-between gap-2">
              <span id="tagLabel" class="rounded-full bg-lagoon/10 px-2 py-0.5 font-mono text-[10px] font-semibold uppercase tracking-wider text-lagoon">Pilihan Terbaik</span>
              <span id="tagPrice" class="whitespace-nowrap font-mono text-sm font-bold text-ink">Premium<span class="font-normal text-ink/50"></span></span>
            </div>
            <p id="tagName" class="mt-1.5 font-display text-base font-semibold leading-tight text-ink">Seafood KIAT Frozen</p>
            <p id="tagOrigin" class="text-[11px] text-ink/50">Surabaya, Jawa Timur</p>
          </div>
        </div>

        <button id="prevSlide" class="absolute left-0 top-[180px] grid h-10 w-10 -translate-x-1/2 place-items-center rounded-full border border-white/15 bg-abyss/70 text-pearl backdrop-blur transition hover:border-gold hover:text-gold sm:top-[210px]"><i data-lucide="chevron-left" class="h-5 w-5"></i></button>
        <button id="nextSlide" class="absolute right-0 top-[180px] grid h-10 w-10 translate-x-1/2 place-items-center rounded-full border border-white/15 bg-abyss/70 text-pearl backdrop-blur transition hover:border-gold hover:text-gold sm:top-[210px]"><i data-lucide="chevron-right" class="h-5 w-5"></i></button>
      </div>
      <div id="heroDots" class="flex justify-center gap-2"></div>
    </div>
  </div>

  <svg viewBox="0 0 1440 110" preserveAspectRatio="none" class="absolute inset-x-0 bottom-[-2px] h-[64px] w-full sm:h-[90px]">
    <path d="M0,45 C220,95 420,5 700,38 C980,72 1200,18 1440,50 L1440,110 L0,110 Z" fill="#F6F1E7"></path>
  </svg>
</section>

<!-- =========================================================
     CATEGORY SECTION (DINAMIS DARI DATABASE)
========================================================== -->
<section id="kategori" class="relative bg-pearl py-20 sm:py-28">
  <div class="mx-auto max-w-7xl px-5 sm:px-8">

    <div class="reveal mb-10 flex flex-wrap items-end justify-between gap-6 sm:mb-14">
      <div>
        <p class="font-mono text-xs font-semibold uppercase tracking-[0.2em] text-lagoon">Pilih Sesuai Selera</p>
        <h2 class="mt-2 font-display text-3xl font-semibold text-ink sm:text-4xl">Jelajahi Kategori</h2>
      </div>
      <div class="flex gap-3">
        <button id="catPrev" class="grid h-11 w-11 place-items-center rounded-full border border-ink/10 bg-white text-ink shadow-sm transition hover:border-gold hover:text-gold"><i data-lucide="chevron-left" class="h-5 w-5"></i></button>
        <button id="catNext" class="grid h-11 w-11 place-items-center rounded-full border border-ink/10 bg-white text-ink shadow-sm transition hover:border-gold hover:text-gold"><i data-lucide="chevron-right" class="h-5 w-5"></i></button>
      </div>
    </div>

    <div id="categoryRow" class="reveal no-scrollbar flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-smooth pb-4">
      
      {{-- LOOPING KATEGORI DINAMIS --}}
      @forelse($categories as $cat)
          <a href="/produk/{{ $cat->slug }}" tabindex="0" class="category-card group relative h-[380px] w-[260px] flex-none snap-start overflow-hidden rounded-3xl shadow-lg sm:h-[420px] sm:w-[300px]">
            <img src="{{ $cat->image ? asset('storage/' . $cat->image) : 'https://placehold.co/400x500?text=' . urlencode($cat->name) }}" alt="{{ $cat->name }}" class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-110" />
            <div class="absolute inset-0 bg-gradient-to-t from-ink/90 via-ink/25 to-transparent"></div>
            <div class="absolute inset-x-0 bottom-0 p-5">
              <h3 class="font-display text-xl font-semibold text-pearl sm:text-2xl">{{ $cat->name }}</h3>
              <span class="mt-1.5 block h-[2px] w-8 bg-gold transition-all duration-500 group-hover:w-16"></span>
              <p class="cat-sub text-xs text-pearl/70">Kategori Pilihan</p>
            </div>
          </a>
      @empty
          <div class="w-full text-center py-16 bg-white rounded-3xl shadow border-2 border-dashed border-ink/10">
            <div class="text-4xl mb-2">🐟</div>
            <p class="text-ink/50 font-medium italic">Belum ada kategori yang ditampilkan.</p>
          </div>
      @endforelse

    </div>
  </div>
</section>

<!-- =========================================================
     TENTANG KAMI
========================================================== -->
<section id="tentang" class="reveal relative bg-white py-16 sm:py-20">
  <div class="mx-auto grid max-w-7xl grid-cols-1 items-center gap-10 px-5 sm:px-8 md:grid-cols-3">
    <div class="flex items-center gap-4">
      <span class="grid h-12 w-12 flex-none place-items-center rounded-full bg-lagoon/10"><i data-lucide="anchor" class="h-5 w-5 text-lagoon"></i></span>
      <div><p class="font-display text-lg font-semibold text-ink">Langsung dari Nelayan</p><p class="text-sm text-ink/60">Rantai pasok pendek, tanpa tengkulak berlapis.</p></div>
    </div>
    <div class="flex items-center gap-4">
      <span class="grid h-12 w-12 flex-none place-items-center rounded-full bg-lagoon/10"><i data-lucide="snowflake" class="h-5 w-5 text-lagoon"></i></span>
      <div><p class="font-display text-lg font-semibold text-ink">Rantai Dingin Terjaga</p><p class="text-sm text-ink/60">Dari kapal hingga pintu rumah, suhu tetap stabil.</p></div>
    </div>
    <div class="flex items-center gap-4">
      <span class="grid h-12 w-12 flex-none place-items-center rounded-full bg-lagoon/10"><i data-lucide="badge-check" class="h-5 w-5 text-lagoon"></i></span>
      <div><p class="font-display text-lg font-semibold text-ink">Bersertifikat Halal MUI</p><p class="text-sm text-ink/60">Diproses sesuai standar higienitas &amp; kehalalan.</p></div>
    </div>
  </div>
</section>

<!-- =========================================================
     FOOTER
========================================================== -->
<footer class="bg-abyss pt-16">
  <div class="mx-auto max-w-7xl px-5 sm:px-8">
    <div class="grid grid-cols-1 gap-10 border-b border-white/10 pb-12 sm:grid-cols-2 lg:grid-cols-4">
      <div>
        <div class="flex items-center gap-3">
          <img src="{{ asset('storage/Logo_Kiat.png') }}" alt="Logo Karya Inti Alam Tunggal"
            class="h-11 w-11 rounded-full object-cover shadow-md border border-white/20" />
          <span class="font-display text-lg font-semibold text-pearl">KIAT</span>
        </div>
        <p class="mt-4 max-w-xs text-sm leading-relaxed text-pearl/50">Seafood premium, dari laut Nusantara ke meja Anda. Segar, higienis, dan bersertifikat Halal.</p>
        <div class="mt-5 flex gap-2.5">
          <a href="#" class="grid h-9 w-9 place-items-center rounded-full border border-white/15 text-pearl/70 transition hover:border-gold hover:text-gold"><i data-lucide="instagram" class="h-4 w-4"></i></a>
          <a href="#" class="grid h-9 w-9 place-items-center rounded-full border border-white/15 text-pearl/70 transition hover:border-gold hover:text-gold"><i data-lucide="phone" class="h-4 w-4"></i></a>
          <a href="#" class="grid h-9 w-9 place-items-center rounded-full border border-white/15 text-pearl/70 transition hover:border-gold hover:text-gold"><i data-lucide="mail" class="h-4 w-4"></i></a>
        </div>
      </div>
      <div>
        <p class="font-mono text-xs font-semibold uppercase tracking-[0.16em] text-pearl/40">Belanja</p>
        <ul class="mt-4 space-y-3 text-sm text-pearl/70">
          <li><a href="{{ route('produk.kategori') }}" class="hover:text-gold">Katalog Produk</a></li>
          <li><a href="#promo" class="hover:text-gold">Promo</a></li>
          <li><a href="#" class="hover:text-gold">Cara Pemesanan</a></li>
        </ul>
      </div>
      <div>
        <p class="font-mono text-xs font-semibold uppercase tracking-[0.16em] text-pearl/40">Perusahaan</p>
        <ul class="mt-4 space-y-3 text-sm text-pearl/70">
          <li><a href="#tentang" class="hover:text-gold">Tentang Kami</a></li>
          <li><a href="#" class="hover:text-gold">Karier</a></li>
          <li><a href="#" class="hover:text-gold">Kontak</a></li>
        </ul>
      </div>
      <div>
        <p class="font-mono text-xs font-semibold uppercase tracking-[0.16em] text-pearl/40">Hubungi Kami</p>
        <ul class="mt-4 space-y-3 text-sm text-pearl/70">
          <li class="flex items-center gap-2"><i data-lucide="map-pin" class="h-4 w-4 text-gold"></i>Surabaya, Jawa Timur</li>
          <li class="flex items-center gap-2"><i data-lucide="phone" class="h-4 w-4 text-gold"></i>+62 812-0000-0000</li>
          <li class="flex items-center gap-2"><i data-lucide="mail" class="h-4 w-4 text-gold"></i>halo@kiat-seafood.id</li>
        </ul>
      </div>
    </div>
    <div class="flex flex-col items-center justify-between gap-3 py-6 text-xs text-pearl/40 sm:flex-row">
      <p>&copy; 2026 Karya Inti Alam Tunggal (KIAT). Seluruh hak cipta dilindungi.</p>
      <p class="flex items-center gap-2">Higienis <span class="text-white/20">·</span> Halal MUI <span class="text-white/20">·</span> Fresh Guarantee</p>
    </div>
  </div>
</footer>

<!-- =========================================================
     MINI CART DRAWER
========================================================== -->
<div id="cartOverlay" class="pointer-events-none fixed inset-0 z-[60] bg-ink/50 opacity-0 backdrop-blur-sm transition-opacity duration-300"></div>

<aside id="cartPanel" class="fixed right-0 top-0 z-[70] flex h-full w-full max-w-md translate-x-full flex-col bg-pearl shadow-2xl transition-transform duration-300 ease-out">
  <div class="flex items-center justify-between border-b border-ink/10 px-6 py-5">
    <div class="flex items-center gap-2.5"><i data-lucide="shopping-cart" class="h-5 w-5 text-lagoon"></i><h3 class="font-display text-lg font-semibold text-ink">Keranjang Saya</h3></div>
    <button id="closeCart" class="grid h-9 w-9 place-items-center rounded-full text-ink/50 transition hover:bg-ink/5 hover:text-ink"><i data-lucide="x" class="h-5 w-5"></i></button>
  </div>
  <div id="cartItemsContainer" class="flex-1 space-y-4 overflow-y-auto px-6 py-5"></div>
  <div class="space-y-4 border-t border-ink/10 px-6 py-5">
    <div class="flex items-center justify-between"><span class="font-mono text-xs uppercase tracking-wider text-ink/50">Subtotal</span><span id="cartSubtotal" class="font-mono text-base font-bold text-ink">Rp0</span></div>
    <div class="flex items-center gap-2 text-xs text-ink/50"><i data-lucide="badge-check" class="h-4 w-4 flex-none text-lagoon"></i>Dikemas dingin &amp; higienis — bersertifikat Halal MUI</div>
    {{-- Tombol checkout drawer → diarahkan ke halaman checkout backend oleh JS. --}}
    <button id="checkoutDrawerBtn" class="btn-shine w-full rounded-full bg-gold py-3.5 font-semibold text-abyss shadow-glow transition-transform hover:scale-[1.02] active:scale-[0.98]"><span class="shine"></span>Checkout Sekarang</button>
  </div>
</aside>

<div id="toast" class="pointer-events-none fixed bottom-6 left-1/2 z-[80] -translate-x-1/2 translate-y-3 rounded-full bg-abyss px-5 py-3 text-sm font-medium text-pearl opacity-0 shadow-2xl transition-all duration-300"><span id="toastText"></span></div>

<!-- =========================================================
     JAVASCRIPT
========================================================== -->
@php
// ---------------------------------------------------------------------
// DATA UNTUK MINI CART DRAWER (disuntik dari backend ke JavaScript)
// ---------------------------------------------------------------------
// $heroSlides : produk DB yang dipakai slider Hero + quick-add (product_id ASLI).
// $initialCart: isi keranjang session saat ini, di-render langsung tanpa fetch awal.
$dashProducts = isset($products) ? $products : collect();
$heroSlides = $dashProducts->map(function ($p) {
  return [
    'id' => $p->id,
    'name' => $p->name,
    'origin' => optional($p->category)->name ?? 'KIAT Seafood',
    'tag' => 'Pilihan Terbaik',
    'price' => (int) $p->price_per_kg,
    'unit' => $p->satuan ?? 'Kg',
    'min' => (int) ($p->min_pembelian ?? 1),
    'stock' => (int) $p->stock,
    'img' => $p->primaryImage?->path
      ? asset('storage/' . $p->primaryImage->path)
      : asset('storage/gambardepan.jpeg'),
    'alt' => $p->name,
  ];
})->values();
$initialCart = array_values(session('cart', []));
@endphp
<script>
  // =====================================================================
  // INTEGRASI BACKEND KERANJANG (session-based Laravel, via Fetch API)
  // ---------------------------------------------------------------------
  // Tidak ada lagi array dummy. heroProducts diisi dari DATABASE ($heroSlides)
  // dan `cart` di-seed dari keranjang SESSION ($initialCart). Semua aksi
  // (add/qty/remove) menembak endpoint CartController & menyegarkan drawer.
  // =====================================================================
  const CART_CSRF = '{{ csrf_token() }}';
  const CART_ENDPOINTS = {
    data:       "{{ route('cart.data') }}",   // GET  snapshot keranjang (JSON)
    add:        "{{ route('cart.add') }}",    // POST tambah item
    base:       "{{ url('/cart') }}",         // + '/{id}/qty' (POST) atau '/{id}' (DELETE)
    checkout:   "{{ route('checkout.index') }}",
    katalog:    "{{ route('produk.kategori') }}",
  };

  // Slide Hero dari DB; fallback 1 slide dekoratif bila belum ada produk aktif.
  let heroProducts = @json($heroSlides);
  if (!Array.isArray(heroProducts) || heroProducts.length === 0) {
    heroProducts = [{
      id: null, name: 'Seafood KIAT Frozen', origin: 'Surabaya, Jawa Timur',
      tag: 'Pilihan Terbaik', price: 0, unit: '', min: 1, stock: 0,
      img: '{{ asset("storage/gambardepan.jpeg") }}', alt: 'Produk KIAT Frozen',
    }];
  }

  // State keranjang di-seed dari session server (hindari kedip kosong), lalu
  // selalu di-replace oleh snapshot JSON backend setiap kali ada perubahan.
  let cart = @json($initialCart);
  function formatRupiah(n) { return n ? 'Rp' + n.toLocaleString('id-ID') : ''; }

  function renderIcons() {
    try { if (window.lucide && typeof window.lucide.createIcons === 'function') { window.lucide.createIcons(); } } catch (err) { console.warn('Lucide gagal dimuat:', err); }
  }

  const revealEls = document.querySelectorAll('.reveal');
  const io = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) { entry.target.classList.add('reveal-visible'); io.unobserve(entry.target); }
    });
  }, { threshold: 0.15 });
  revealEls.forEach((el) => io.observe(el));

  const siteNav = document.getElementById('siteNav');
  window.addEventListener('scroll', () => {
    if (window.scrollY > 12) { siteNav.classList.add('bg-abyss/95', 'shadow-lg', 'shadow-black/20'); siteNav.classList.remove('bg-abyss/70'); } else { siteNav.classList.remove('bg-abyss/95', 'shadow-lg', 'shadow-black/20'); siteNav.classList.add('bg-abyss/70'); }
  });

  const menuToggle = document.getElementById('menuToggle');
  const mobilePanel = document.getElementById('mobilePanel');
  const menuIconOpen = document.getElementById('menuIconOpen');
  const menuIconClose = document.getElementById('menuIconClose');
  menuToggle.addEventListener('click', () => {
    const isHidden = mobilePanel.classList.contains('hidden');
    mobilePanel.classList.toggle('hidden'); mobilePanel.classList.toggle('flex');
    menuIconOpen.classList.toggle('hidden', isHidden); menuIconClose.classList.toggle('hidden', !isHidden);
  });

  let currentSlide = 0;
  let autoplayTimer = null;
  const heroImg = document.getElementById('heroImg');
  const tagLabel = document.getElementById('tagLabel');
  const tagPrice = document.getElementById('tagPrice');
  const tagName = document.getElementById('tagName');
  const tagOrigin = document.getElementById('tagOrigin');
  const catchTag = document.getElementById('catchTag');
  const heroDots = document.getElementById('heroDots');

  function renderDots() {
    heroDots.innerHTML = heroProducts.map((_, i) => `<button data-dot="${i}" class="h-2 rounded-full transition-all duration-300 ${i === currentSlide ? 'w-6 bg-gold' : 'w-2 bg-pearl/30 hover:bg-pearl/50'}"></button>`).join('');
    heroDots.querySelectorAll('[data-dot]').forEach((btn) => { btn.addEventListener('click', () => { goToSlide(parseInt(btn.dataset.dot, 10)); restartAutoplay(); }); });
  }

  function goToSlide(index) {
    currentSlide = (index + heroProducts.length) % heroProducts.length;
    const p = heroProducts[currentSlide];
    heroImg.classList.add('opacity-0'); catchTag.classList.add('opacity-0');
    setTimeout(() => {
      heroImg.src = p.img; heroImg.alt = p.alt; tagLabel.textContent = p.tag;
      tagPrice.innerHTML = formatRupiah(p.price) + (p.unit ? '<span class="font-normal text-ink/50">/' + p.unit + '</span>' : '');
      tagName.textContent = p.name; tagOrigin.textContent = p.origin;
      heroImg.classList.remove('opacity-0'); catchTag.classList.remove('opacity-0');
    }, 180);
    renderDots();
  }

  function restartAutoplay() { clearInterval(autoplayTimer); autoplayTimer = setInterval(() => goToSlide(currentSlide + 1), 6000); }
  document.getElementById('prevSlide').addEventListener('click', () => { goToSlide(currentSlide - 1); restartAutoplay(); });
  document.getElementById('nextSlide').addEventListener('click', () => { goToSlide(currentSlide + 1); restartAutoplay(); });
  renderDots(); restartAutoplay();

  const heroTilt = document.getElementById('heroTilt');
  const heroCardInner = document.getElementById('heroCardInner');
  heroTilt.addEventListener('mousemove', (e) => {
    const rect = heroTilt.getBoundingClientRect();
    const x = (e.clientX - rect.left) / rect.width - 0.5; const y = (e.clientY - rect.top) / rect.height - 0.5;
    heroCardInner.style.transform = `rotateY(${x * 10}deg) rotateX(${-y * 10}deg) scale(1.015)`;
  });
  heroTilt.addEventListener('mouseleave', () => { heroCardInner.style.transform = 'rotateY(0deg) rotateX(0deg) scale(1)'; });

  const categoryRow = document.getElementById('categoryRow');
  document.getElementById('catPrev').addEventListener('click', () => categoryRow.scrollBy({ left: -320, behavior: 'smooth' }));
  document.getElementById('catNext').addEventListener('click', () => categoryRow.scrollBy({ left: 320, behavior: 'smooth' }));

  const cartBtn = document.getElementById('cartBtn'); const cartPanel = document.getElementById('cartPanel');
  const cartOverlay = document.getElementById('cartOverlay'); const closeCartBtn = document.getElementById('closeCart');
  const cartBadge = document.getElementById('cartBadge'); const cartItemsContainer = document.getElementById('cartItemsContainer');
  const cartSubtotalEl = document.getElementById('cartSubtotal');

  function openCart() { cartPanel.classList.remove('translate-x-full'); cartOverlay.classList.remove('opacity-0', 'pointer-events-none'); document.documentElement.classList.add('overflow-hidden'); }
  function closeCart() { cartPanel.classList.add('translate-x-full'); cartOverlay.classList.add('opacity-0', 'pointer-events-none'); document.documentElement.classList.remove('overflow-hidden'); }
  cartBtn.addEventListener('click', openCart); closeCartBtn.addEventListener('click', closeCart); cartOverlay.addEventListener('click', closeCart);

  // ---------------------------------------------------------------------
  // RENDER MINI CART DRAWER dari state `cart`. Bentuk tiap item mengikuti
  // struktur keranjang SESSION Laravel: { product_id, name, price, qty,
  // satuan, image (path), subtotal }.
  // ---------------------------------------------------------------------
  function renderCart() {
    const totalQty = cart.reduce((sum, it) => sum + Number(it.qty), 0);
    const subtotal = cart.reduce((sum, it) => sum + Number(it.subtotal), 0);
    cartBadge.textContent = totalQty;                                  // badge = total kuantitas
    cartSubtotalEl.textContent = 'Rp' + subtotal.toLocaleString('id-ID');

    if (cart.length === 0) {
      cartItemsContainer.innerHTML = `<div class="flex h-full flex-col items-center justify-center gap-3 py-16 text-center"><i data-lucide="shopping-cart" class="h-10 w-10 text-ink/20"></i><p class="text-sm text-ink/50">Keranjang kamu masih kosong.</p></div>`;
    } else {
      cartItemsContainer.innerHTML = cart.map((item) => {
        // Path gambar dari session ('products/xx.jpg') → URL publik '/storage/...'.
        const img = item.image ? ('/storage/' + item.image) : 'https://placehold.co/64x64?text=KIAT';
        const sub = Number(item.subtotal).toLocaleString('id-ID');
        return `
        <div class="flex gap-3 border-b border-ink/5 pb-4">
          <img src="${img}" alt="${item.name}" class="h-16 w-16 flex-none rounded-xl object-cover" />
          <div class="flex-1">
            <div class="flex items-start justify-between gap-2"><p class="font-display text-sm font-semibold leading-snug text-ink">${item.name}</p><button onclick="removeCartItem(${item.product_id})" class="text-ink/30 transition hover:text-coral"><i data-lucide="trash-2" class="h-4 w-4"></i></button></div>
            <p class="text-xs text-ink/50">${item.satuan || ''}</p>
            <div class="mt-2 flex items-center justify-between">
              <div class="flex items-center gap-2 rounded-full border border-ink/10 px-1">
                <button onclick="changeQty(${item.product_id}, 'decrease')" class="grid h-6 w-6 place-items-center text-ink/60 hover:text-ink"><i data-lucide="minus" class="h-3 w-3"></i></button><span class="w-6 text-center font-mono text-xs">${item.qty}</span><button onclick="changeQty(${item.product_id}, 'increase')" class="grid h-6 w-6 place-items-center text-ink/60 hover:text-ink"><i data-lucide="plus" class="h-3 w-3"></i></button>
              </div><span class="font-mono text-sm font-semibold text-ink">Rp${sub}</span>
            </div>
          </div>
        </div>`;
      }).join('');
    }
    renderIcons();
  }

  // Ambil snapshot keranjang terbaru dari server (GET cart.data) → update & render.
  function refreshCart() {
    return fetch(CART_ENDPOINTS.data, { headers: { 'Accept': 'application/json' } })
      .then((r) => r.json())
      .then((d) => { cart = d.items || []; renderCart(); })
      .catch((err) => console.warn('Gagal memuat keranjang:', err));
  }

  // +/- kuantitas → endpoint updateQty (POST, JSON). Backend menegakkan aturan
  // minimal pembelian; setelah itu drawer disegarkan dari snapshot server.
  function changeQty(id, action) {
    fetch(CART_ENDPOINTS.base + '/' + id + '/qty', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CART_CSRF, 'Accept': 'application/json' },
      body: JSON.stringify({ action }),
    })
      .then((r) => r.json())
      .then((res) => {
        if (res && res.success === false && res.at_min) showToast(res.message || 'Sudah di batas minimal pembelian');
        return refreshCart();
      })
      .catch(() => showToast('Terjadi kesalahan jaringan'));
  }

  // Hapus item → endpoint remove (DELETE). Balasannya snapshot keranjang terbaru.
  function removeCartItem(id) {
    fetch(CART_ENDPOINTS.base + '/' + id, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': CART_CSRF, 'Accept': 'application/json' },
    })
      .then((r) => r.json())
      .then((d) => { cart = d.items || []; renderCart(); })
      .catch(() => showToast('Terjadi kesalahan jaringan'));
  }

  // Tambah ke keranjang → endpoint add (POST). id null = slide fallback → ke katalog.
  function addToCart(id, qty) {
    if (!id) { window.location.href = CART_ENDPOINTS.katalog; return; }
    fetch(CART_ENDPOINTS.add, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CART_CSRF, 'Accept': 'application/json' },
      body: JSON.stringify({ product_id: id, qty: qty || null }),
    })
      .then((r) => r.json().then((d) => ({ ok: r.ok, d })))
      .then(({ ok, d }) => {
        if (!ok || !d.success) { showToast(d.message || 'Gagal menambah ke keranjang'); return; }
        cart = d.items || [];                          // snapshot terbaru dari server
        renderCart(); pulseBadge(); openCart();        // update badge/subtotal & buka drawer
        showToast(d.message || 'Ditambahkan ke keranjang');
      })
      .catch(() => showToast('Terjadi kesalahan jaringan'));
  }

  function pulseBadge() { cartBadge.classList.remove('badge-pulse'); void cartBadge.offsetWidth; cartBadge.classList.add('badge-pulse'); }

  // Quick-add dari kartu Hero: kirim product_id ASLI + min pembelian slide aktif.
  document.getElementById('quickAddBtn').addEventListener('click', () => {
    const p = heroProducts[currentSlide];
    if (p && p.id && p.stock <= 0) { showToast('Stok produk ini sedang habis'); return; }
    addToCart(p ? p.id : null, p ? p.min : null);
  });

  // Tombol "Checkout Sekarang" di drawer → lanjut ke halaman checkout backend.
  document.getElementById('checkoutDrawerBtn').addEventListener('click', () => {
    if (!cart.length) { showToast('Keranjang masih kosong'); return; }
    window.location.href = CART_ENDPOINTS.checkout;
  });

  const toastEl = document.getElementById('toast'); const toastTextEl = document.getElementById('toastText'); let toastTimer = null;
  function showToast(message) { toastTextEl.textContent = message; toastEl.classList.remove('opacity-0', 'translate-y-3'); clearTimeout(toastTimer); toastTimer = setTimeout(() => { toastEl.classList.add('opacity-0', 'translate-y-3'); }, 2200); }

  renderCart(); renderIcons();
</script>
</body>
</html>
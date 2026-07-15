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
        <span class="font-display text-lg font-semibold tracking-tight text-pearl">KIAT SURABAYA</span>
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
      <a href="{{ route('user.riwayat') }}"
        class="hidden items-center gap-2 rounded-full px-3 py-2 text-pearl/80 transition hover:bg-white/5 hover:text-gold sm:flex"
        title="Pesanan Saya">
        <i data-lucide="package" class="h-5 w-5"></i>
        <span class="text-xs font-medium">Pesanan</span>
      </a>
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
            <p id="tagOrigin" class="text-[11px] text-ink/50">Lebak Permai Utara 3/19A</p>
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
          <span class="font-display text-lg font-semibold text-pearl">KIAT SURABAYA</span>
        </div>
        <p class="mt-4 max-w-xs text-sm leading-relaxed text-pearl/50">Seafood premium, dari laut Nusantara ke meja Anda. Segar, higienis, dan bersertifikat Halal.</p>
        <div class="mt-5 flex gap-2.5">
          <a href="https://www.instagram.com/kiatsurabaya?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" target="_blank" class="grid h-9 w-9 place-items-center rounded-full border border-white/15 text-pearl/70 transition hover:border-gold hover:text-gold">
                            <!-- SVG Instagram -->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="h-5 w-5">
                                <path fill="#304ffe" d="M41.67,13.48c-0.4,0.26-0.97,0.5-1.21,0.77c-0.09,0.09-0.14,0.19-0.12,0.29v1.03l-0.3,1.01l-0.3,1l-0.33,1.1 l-0.68,2.25l-0.66,2.22l-0.5,1.67c0,0.26-0.01,0.52-0.03,0.77c-0.07,0.96-0.27,1.88-0.59,2.74c-0.19,0.53-0.42,1.04-0.7,1.52 c-0.1,0.19-0.22,0.38-0.34,0.56c-0.4,0.63-0.88,1.21-1.41,1.72c-0.41,0.41-0.86,0.79-1.35,1.11c0,0,0,0-0.01,0 c-0.08,0.07-0.17,0.13-0.27,0.18c-0.31,0.21-0.64,0.39-0.98,0.55c-0.23,0.12-0.46,0.22-0.7,0.31c-0.05,0.03-0.11,0.05-0.16,0.07 c-0.57,0.27-1.23,0.45-1.89,0.54c-0.04,0.01-0.07,0.01-0.11,0.02c-0.4,0.07-0.79,0.13-1.19,0.16c-0.18,0.02-0.37,0.03-0.55,0.03 l-0.71-0.04l-3.42-0.18c0-0.01-0.01,0-0.01,0l-1.72-0.09c-0.13,0-0.27,0-0.4-0.01c-0.54-0.02-1.06-0.08-1.58-0.19 c-0.01,0-0.01,0-0.01,0c-0.95-0.18-1.86-0.5-2.71-0.93c-0.47-0.24-0.93-0.51-1.36-0.82c-0.18-0.13-0.35-0.27-0.52-0.42 c-0.48-0.4-0.91-0.83-1.31-1.27c-0.06-0.06-0.11-0.12-0.16-0.18c-0.06-0.06-0.12-0.13-0.17-0.19c-0.38-0.48-0.7-0.97-0.96-1.49 c-0.24-0.46-0.43-0.95-0.58-1.49c-0.06-0.19-0.11-0.37-0.15-0.57c-0.01-0.01-0.02-0.03-0.02-0.05c-0.1-0.41-0.19-0.84-0.24-1.27 c-0.06-0.33-0.09-0.66-0.09-1c-0.02-0.13-0.02-0.27-0.02-0.4l1.91-2.95l1.87-2.88l0.85-1.31l0.77-1.18l0.26-0.41v-1.03 c0.02-0.23,0.03-0.47,0.02-0.69c-0.01-0.7-0.15-1.38-0.38-2.03c-0.22-0.69-0.53-1.34-0.85-1.94c-0.38-0.69-0.78-1.31-1.11-1.87 C14,7.4,13.66,6.73,13.75,6.26C14.47,6.09,15.23,6,16,6h16c4.18,0,7.78,2.6,9.27,6.26C41.43,12.65,41.57,13.06,41.67,13.48z"></path>
                                <path fill="#4928f4" d="M42,16v0.27l-1.38,0.8l-0.88,0.51l-0.97,0.56l-1.94,1.13l-1.9,1.1l-1.94,1.12l-0.77,0.45 c0,0.48-0.12,0.92-0.34,1.32c-0.31,0.58-0.83,1.06-1.49,1.47c-0.67,0.41-1.49,0.74-2.41,0.98c0,0,0-0.01-0.01,0 c-3.56,0.92-8.42,0.5-10.78-1.26c-0.66-0.49-1.12-1.09-1.32-1.78c-0.06-0.23-0.09-0.48-0.09-0.73v-7.19 c0.01-0.15-0.09-0.3-0.27-0.45c-0.54-0.43-1.81-0.84-3.23-1.25c-1.11-0.31-2.3-0.62-3.3-0.92c-0.79-0.24-1.46-0.48-1.86-0.71 c0.18-0.35,0.39-0.7,0.61-1.03c1.4-2.05,3.54-3.56,6.02-4.13C14.47,6.09,15.23,6,16,6h10.8c5.37,0.94,10.32,3.13,14.47,6.26 c0.16,0.39,0.3,0.8,0.4,1.22c0.18,0.66,0.29,1.34,0.32,2.05C42,15.68,42,15.84,42,16z"></path>
                                <path fill="#6200ea" d="M42,16v4.41l-0.22,0.68l-0.75,2.33l-0.78,2.4l-0.41,1.28l-0.38,1.19l-0.37,1.13l-0.36,1.12l-0.19,0.59 l-0.25,0.78c0,0.76-0.02,1.43-0.07,2c-0.01,0.06-0.02,0.12-0.02,0.18c-0.06,0.53-0.14,0.98-0.27,1.36 c-0.01,0.06-0.03,0.12-0.05,0.17c-0.26,0.72-0.65,1.18-1.23,1.48c-0.14,0.08-0.3,0.14-0.47,0.2c-0.53,0.18-1.2,0.27-2.02,0.32 c-0.6,0.04-1.29,0.05-2.07,0.05H31.4l-1.19-0.05L30,37.61l-2.17-0.09l-2.2-0.09l-7.25-0.3l-1.88-0.08h-0.26 c-0.78-0.01-1.45-0.06-2.03-0.14c-0.84-0.13-1.49-0.35-1.98-0.68c-0.7-0.45-1.11-1.11-1.35-2.03c-0.06-0.22-0.11-0.45-0.14-0.7 c-0.1-0.58-0.15-1.25-0.18-2c0-0.15,0-0.3-0.01-0.46c-0.01-0.01,0-0.01,0-0.01v-0.58c-0.01-0.29-0.01-0.59-0.01-0.9l0.05-1.61 l0.03-1.15l0.04-1.34v-0.19l0.07-2.46l0.07-2.46l0.07-2.31l0.06-2.27l0.02-0.6c0-0.31-1.05-0.49-2.22-0.64 c-0.93-0.12-1.95-0.23-2.56-0.37c0.05-0.23,0.1-0.46,0.16-0.68c0.18-0.72,0.45-1.4,0.79-2.05c0.18-0.35,0.39-0.7,0.61-1.03 c2.16-0.95,4.41-1.69,6.76-2.17c2.06-0.43,4.21-0.66,6.43-0.66c7.36,0,14.16,2.49,19.54,6.69c0.52,0.4,1.03,0.83,1.53,1.28 C42,15.68,42,15.84,42,16z"></path>
                                <path fill="#673ab7" d="M42,18.37v4.54l-0.55,1.06l-1.05,2.05l-0.56,1.08l-0.51,0.99l-0.22,0.43c0,0.31,0,0.61-0.02,0.9 c0,0.43-0.02,0.84-0.05,1.22c-0.04,0.45-0.1,0.86-0.16,1.24c-0.15,0.79-0.36,1.47-0.66,2.03c-0.04,0.07-0.08,0.14-0.12,0.2 c-0.11,0.18-0.24,0.35-0.38,0.51c-0.18,0.22-0.38,0.41-0.61,0.57c-0.34,0.26-0.74,0.47-1.2,0.63c-0.57,0.21-1.23,0.35-2.01,0.43 c-0.51,0.05-1.07,0.08-1.68,0.08l-0.42,0.02l-2.08,0.12h-0.01L27.5,36.6l-2.25,0.13l-3.1,0.18l-3.77,0.22l-0.55,0.03 c-0.51,0-0.99-0.03-1.45-0.09c-0.05-0.01-0.09-0.02-0.14-0.02c-0.68-0.11-1.3-0.29-1.86-0.54c-0.68-0.3-1.27-0.7-1.77-1.18 c-0.44-0.43-0.82-0.92-1.13-1.47c-0.07-0.13-0.14-0.25-0.2-0.39c-0.3-0.59-0.54-1.25-0.72-1.97c-0.03-0.12-0.06-0.25-0.08-0.38 c-0.06-0.23-0.11-0.47-0.14-0.72c-0.11-0.64-0.17-1.32-0.2-2.03v-0.01c-0.01-0.29-0.02-0.57-0.02-0.87l-0.49-1.17l-0.07-0.18 L9.5,25.99L8.75,24.2l-0.12-0.29l-0.72-1.73l-0.8-1.93c0,0,0,0-0.01,0L6.29,18.3L6,17.59V16c0-0.63,0.06-1.25,0.17-1.85 c0.05-0.23,0.1-0.46,0.16-0.68c0.85-0.49,1.74-0.94,2.65-1.34c2.08-0.93,4.31-1.62,6.62-2.04c1.72-0.31,3.51-0.48,5.32-0.48 c7.31,0,13.94,2.65,19.12,6.97c0.2,0.16,0.39,0.32,0.58,0.49C41.09,17.48,41.55,17.91,42,18.37z"></path>
                                <path fill="#8e24aa" d="M42,21.35v5.14l-0.57,1.19l-1.08,2.25l-0.01,0.03c0,0.43-0.02,0.82-0.05,1.17c-0.1,1.15-0.38,1.88-0.84,2.33 c-0.33,0.34-0.74,0.53-1.25,0.63c-0.03,0.01-0.07,0.01-0.1,0.02c-0.16,0.03-0.33,0.05-0.51,0.05c-0.62,0.06-1.35,0.02-2.19-0.04 c-0.09,0-0.19-0.01-0.29-0.02c-0.61-0.04-1.26-0.08-1.98-0.11c-0.39-0.01-0.8-0.02-1.22-0.02h-0.02l-1.01,0.08h-0.01l-2.27,0.16 l-2.59,0.2l-0.38,0.03l-3.03,0.22l-1.57,0.12l-1.55,0.11c-0.27,0-0.53,0-0.79-0.01c0,0-0.01-0.01-0.01,0 c-1.13-0.02-2.14-0.09-3.04-0.26c-0.83-0.14-1.56-0.36-2.18-0.69c-0.64-0.31-1.17-0.75-1.6-1.31c-0.41-0.55-0.71-1.24-0.9-2.07 c0-0.01,0-0.01,0-0.01c-0.14-0.67-0.22-1.45-0.22-2.33l-0.15-0.27L9.7,26.35l-0.13-0.22L9.5,25.99l-0.93-1.65l-0.46-0.83 l-0.58-1.03l-1-1.79L6,19.75v-3.68c0.88-0.58,1.79-1.09,2.73-1.55c1.14-0.58,2.32-1.07,3.55-1.47c1.34-0.44,2.74-0.79,4.17-1.02 c1.45-0.24,2.94-0.36,4.47-0.36c6.8,0,13.04,2.43,17.85,6.47c0.22,0.17,0.43,0.36,0.64,0.54c0.84,0.75,1.64,1.56,2.37,2.41 C41.86,21.18,41.94,21.26,42,21.35z"></path>
                                <path fill="#c2185b" d="M42,24.71v7.23c-0.24-0.14-0.57-0.31-0.98-0.49c-0.22-0.11-0.47-0.22-0.73-0.32 c-0.38-0.17-0.79-0.33-1.25-0.49c-0.1-0.04-0.2-0.07-0.31-0.1c-0.18-0.07-0.37-0.13-0.56-0.19c-0.59-0.18-1.24-0.35-1.92-0.5 c-0.26-0.05-0.53-0.1-0.8-0.14c-0.87-0.15-1.8-0.24-2.77-0.25c-0.08-0.01-0.17-0.01-0.25-0.01l-2.57,0.02l-3.5,0.02h-0.01 l-7.49,0.06c-2.38,0-3.84,0.57-4.72,0.8c0,0-0.01,0-0.01,0.01c-0.93,0.24-1.22,0.09-1.3-1.54c-0.02-0.45-0.03-1.03-0.03-1.74 l-0.56-0.43l-0.98-0.74l-0.6-0.46l-0.12-0.09L8.88,24.1l-0.25-0.19l-0.52-0.4l-0.96-0.72L6,21.91v-3.4 c0.1-0.08,0.19-0.15,0.29-0.21c1.45-1,3-1.85,4.64-2.54c1.46-0.62,3-1.11,4.58-1.46c0.43-0.09,0.87-0.18,1.32-0.24 c1.33-0.23,2.7-0.34,4.09-0.34c6.01,0,11.53,2.09,15.91,5.55c0.66,0.52,1.3,1.07,1.9,1.66c0.82,0.78,1.59,1.61,2.3,2.49 c0.14,0.18,0.28,0.36,0.42,0.55C41.64,24.21,41.82,24.46,42,24.71z"></path>
                                <path fill="#d81b60" d="M42,28.72V32c0,0.65-0.06,1.29-0.18,1.91c-0.18,0.92-0.49,1.8-0.91,2.62c-0.22,0.05-0.47,0.05-0.75,0.01 c-0.63-0.11-1.37-0.44-2.17-0.87c-0.04-0.01-0.08-0.03-0.11-0.05c-0.25-0.13-0.51-0.27-0.77-0.43c-0.53-0.29-1.09-0.61-1.65-0.91 c-0.12-0.06-0.24-0.12-0.35-0.18c-0.64-0.33-1.3-0.63-1.96-0.86c0,0,0,0-0.01,0c-0.14-0.05-0.29-0.1-0.44-0.14 c-0.57-0.16-1.15-0.26-1.71-0.26l-1.1-0.32l-4.87-1.41c0,0,0,0-0.01,0l-2.99-0.87h-0.01l-1.3-0.38c-3.76,0-6.07,1.6-7.19,0.99 c-0.44-0.23-0.7-0.81-0.79-1.95c-0.03-0.32-0.04-0.68-0.04-1.1l-1.17-0.57l-0.05-0.02h-0.01l-0.84-0.42L9.7,26.35l-0.07-0.03 l-0.17-0.09L7.5,25.28L6,24.55v-3.43c0.17-0.15,0.35-0.29,0.53-0.43c0.19-0.15,0.38-0.29,0.57-0.44c0.01,0,0.01,0,0.01,0 c1.18-0.85,2.43-1.6,3.76-2.22c1.55-0.74,3.2-1.31,4.91-1.68c0.25-0.06,0.51-0.12,0.77-0.16c1.42-0.27,2.88-0.41,4.37-0.41 c5.27,0,10.11,1.71,14.01,4.59c1.13,0.84,2.18,1.77,3.14,2.78c0.79,0.83,1.52,1.73,2.18,2.67c0.05,0.07,0.1,0.14,0.15,0.2 c0.37,0.54,0.71,1.09,1.03,1.66C41.64,28.02,41.82,28.37,42,28.72z"></path>
                                <path fill="#f50057" d="M41.82,33.91c-0.18,0.92-0.49,1.8-0.91,2.62c-0.19,0.37-0.4,0.72-0.63,1.06c-0.14,0.21-0.29,0.41-0.44,0.6 c-0.36-0.14-0.89-0.34-1.54-0.56c0,0,0,0,0-0.01c-0.49-0.17-1.05-0.35-1.65-0.52c-0.17-0.05-0.34-0.1-0.52-0.15 c-0.71-0.19-1.45-0.36-2.17-0.46c-0.6-0.1-1.19-0.16-1.74-0.16l-0.46-0.13h-0.01l-2.42-0.7l-1.49-0.43l-1.66-0.48h-0.01l-0.54-0.15 l-6.53-1.88l-1.88-0.54l-1.4-0.33l-2.28-0.54l-0.28-0.07c0,0,0,0-0.01,0l-2.29-0.53c0-0.01,0-0.01,0-0.01l-0.41-0.09l-0.21-0.05 l-1.67-0.39l-0.19-0.05l-1.42-1.17L6,27.9v-4.08c0.37-0.36,0.75-0.7,1.15-1.03c0.12-0.11,0.25-0.21,0.38-0.31 c0.12-0.1,0.25-0.2,0.38-0.3c0.91-0.69,1.87-1.31,2.89-1.84c1.3-0.7,2.68-1.26,4.13-1.66c0.28-0.09,0.56-0.17,0.85-0.23 c1.64-0.41,3.36-0.62,5.14-0.62c4.47,0,8.63,1.35,12.07,3.66c1.71,1.15,3.25,2.53,4.55,4.1c0.66,0.79,1.26,1.62,1.79,2.5 c0.05,0.07,0.09,0.13,0.13,0.2c0.32,0.53,0.62,1.08,0.89,1.64c0.25,0.5,0.47,1,0.67,1.52C41.34,32.25,41.6,33.07,41.82,33.91z"></path>
                                <path fill="#ff1744" d="M40.28,37.59c-0.14,0.21-0.29,0.41-0.44,0.6c-0.44,0.55-0.92,1.05-1.46,1.49c-0.47,0.39-0.97,0.74-1.5,1.04 c-0.2-0.05-0.4-0.11-0.61-0.19c-0.66-0.23-1.35-0.61-1.99-1.01c-0.96-0.61-1.79-1.27-2.16-1.57c-0.14-0.12-0.21-0.18-0.21-0.18 l-1.7-0.15L30,37.6l-2.2-0.19l-2.28-0.2l-3.37-0.3l-5.34-0.47l-0.02-0.01l-1.88-0.91l-1.9-0.92l-1.53-0.74l-0.33-0.16l-0.41-0.2 l-1.42-0.69L7.43,31.9l-0.59-0.29L6,31.35v-4.47c0.47-0.56,0.97-1.09,1.5-1.6c0.34-0.32,0.7-0.64,1.07-0.94 c0.06-0.05,0.12-0.1,0.18-0.14c0.04-0.05,0.09-0.08,0.13-0.1c0.59-0.48,1.21-0.91,1.85-1.3c0.74-0.47,1.52-0.89,2.33-1.24 c0.87-0.39,1.78-0.72,2.72-0.97c1.63-0.46,3.36-0.7,5.14-0.7c4.08,0,7.85,1.24,10.96,3.37c1.99,1.36,3.71,3.08,5.07,5.07 c0.45,0.64,0.85,1.32,1.22,2.02c0.13,0.26,0.26,0.52,0.37,0.78c0.12,0.25,0.23,0.5,0.34,0.75c0.21,0.52,0.4,1.04,0.57,1.58 c0.32,1,0.56,2.02,0.71,3.08C40.21,36.89,40.25,37.24,40.28,37.59z"></path>
                                <path fill="#ff5722" d="M38.39,39.42c0,0.08,0,0.17-0.01,0.26c-0.47,0.39-0.97,0.74-1.5,1.04c-0.22,0.12-0.44,0.24-0.67,0.34 c-0.23,0.11-0.46,0.21-0.7,0.3c-0.34-0.18-0.8-0.4-1.29-0.61c-0.69-0.31-1.44-0.59-2.02-0.68c-0.14-0.03-0.27-0.04-0.39-0.04 l-1.64-0.21h-0.02l-2.04-0.27l-2.06-0.27l-0.96-0.12l-7.56-0.98c-0.49,0-1.01-0.03-1.55-0.1c-0.66-0.06-1.35-0.16-2.04-0.3 c-0.68-0.12-1.37-0.28-2.03-0.45c-0.69-0.16-1.37-0.35-2-0.53c-0.73-0.22-1.41-0.43-1.98-0.62c-0.47-0.15-0.87-0.29-1.18-0.4 c-0.18-0.43-0.33-0.88-0.44-1.34C6.1,33.66,6,32.84,6,32v-1.67c0.32-0.53,0.67-1.05,1.06-1.54c0.71-0.94,1.52-1.8,2.4-2.56 c0.03-0.04,0.07-0.07,0.1-0.09l0.01-0.01c0.31-0.28,0.63-0.53,0.97-0.77c0.04-0.04,0.08-0.07,0.12-0.1 c0.16-0.12,0.33-0.24,0.51-0.35c1.43-0.97,3.01-1.73,4.7-2.24c1.6-0.48,3.29-0.73,5.05-0.73c3.49,0,6.75,1.03,9.47,2.79 c2.01,1.29,3.74,2.99,5.06,4.98c0.16,0.23,0.31,0.46,0.46,0.7c0.69,1.17,1.26,2.43,1.68,3.75c0.05,0.15,0.09,0.3,0.13,0.46 c0.08,0.27,0.15,0.55,0.21,0.83c0.02,0.07,0.04,0.14,0.06,0.22c0.14,0.63,0.24,1.29,0.31,1.95c0,0.01,0,0.01,0,0.01 C38.36,38.22,38.39,38.82,38.39,39.42z"></path>
                                <path fill="#ff6f00" d="M36.33,39.42c0,0.35-0.02,0.73-0.06,1.11c-0.02,0.18-0.04,0.36-0.06,0.53c-0.23,0.11-0.46,0.21-0.7,0.3 c-0.45,0.17-0.91,0.31-1.38,0.41c-0.32,0.07-0.65,0.13-0.98,0.16h-0.01c-0.31-0.19-0.67-0.42-1.04-0.68 c-0.67-0.47-1.37-1-1.93-1.43c-0.01-0.01-0.01-0.01-0.02-0.02c-0.59-0.45-1.01-0.79-1.01-0.79l-1.06,0.04l-2.04,0.07l-0.95,0.04 l-3.82,0.14l-3.23,0.12c-0.21,0.01-0.46,0.01-0.77,0h-0.01c-0.42-0.01-0.92-0.04-1.47-0.09c-0.64-0.05-1.34-0.11-2.05-0.18 c-0.69-0.08-1.39-0.16-2.06-0.24c-0.74-0.08-1.44-0.17-2.04-0.25c-0.47-0.06-0.88-0.11-1.21-0.15c-0.28-0.32-0.53-0.65-0.77-1.01 c-0.36-0.54-0.67-1.11-0.91-1.72c-0.18-0.43-0.33-0.88-0.44-1.34c0.29-0.89,0.67-1.73,1.12-2.54c0.36-0.66,0.78-1.29,1.24-1.89 c0.45-0.59,0.94-1.14,1.47-1.64v-0.01c0.15-0.15,0.3-0.29,0.45-0.42c0.28-0.26,0.57-0.5,0.87-0.73h0.01 c0.01-0.02,0.02-0.02,0.03-0.03c0.24-0.19,0.49-0.36,0.74-0.53c1.48-1.01,3.15-1.76,4.95-2.2c1.19-0.29,2.44-0.45,3.73-0.45 c2.54,0,4.94,0.61,7.05,1.71h0.01c1.81,0.93,3.41,2.21,4.7,3.75c0.71,0.82,1.32,1.72,1.82,2.67c0.35,0.64,0.65,1.31,0.9,1.99 c0.02,0.06,0.04,0.11,0.06,0.16c0.17,0.5,0.32,1.02,0.45,1.54c0.09,0.37,0.16,0.75,0.22,1.13c0.02,0.12,0.04,0.23,0.05,0.35 C36.28,37.99,36.33,38.7,36.33,39.42z"></path>
                                <path fill="#ff9800" d="M34.28,39.42v0.1c0,0.34-0.03,0.77-0.06,1.23c-0.03,0.34-0.06,0.69-0.09,1.02c-0.32,0.07-0.65,0.13-0.98,0.16 h-0.01C32.76,41.98,32.39,42,32,42h-1.75l-0.38-0.11l-1.97-0.6l-2-0.6l-4.63-1.39l-2-0.6c0,0-0.83,0.33-2,0.72h-0.01 c-0.45,0.15-0.94,0.31-1.46,0.47c-0.65,0.19-1.34,0.38-2.02,0.53c-0.7,0.16-1.39,0.28-2.01,0.33c-0.19,0.02-0.38,0.03-0.55,0.03 c-0.56-0.31-1.1-0.68-1.59-1.09c-0.43-0.36-0.83-0.75-1.2-1.18c-0.28-0.32-0.53-0.65-0.77-1.01c0.07-0.45,0.15-0.89,0.27-1.32 c0.3-1.19,0.77-2.33,1.39-3.37c0.34-0.59,0.72-1.16,1.16-1.69c0.01-0.03,0.04-0.06,0.07-0.08c-0.01-0.01,0-0.01,0-0.01 c0.13-0.17,0.27-0.33,0.41-0.48c0-0.01,0-0.01,0-0.01c0.41-0.44,0.83-0.86,1.29-1.25c0.16-0.13,0.31-0.26,0.48-0.39 c0.03-0.03,0.06-0.05,0.1-0.08c2.25-1.72,5.06-2.76,8.09-2.76c3.44,0,6.57,1.29,8.94,3.41c1.14,1.03,2.11,2.26,2.84,3.63 c0.06,0.1,0.12,0.21,0.17,0.32c0.09,0.18,0.18,0.37,0.26,0.57c0.33,0.72,0.59,1.48,0.77,2.26c0.02,0.08,0.04,0.16,0.06,0.24 c0.08,0.37,0.15,0.75,0.2,1.13C34.24,38.21,34.28,38.81,34.28,39.42z"></path>
                                <path fill="#ffc107" d="M32.22,39.42c0,0.2-0.01,0.42-0.02,0.65c-0.02,0.37-0.05,0.77-0.1,1.18c-0.02,0.25-0.06,0.5-0.1,0.75h-5.48 l-1.06-0.17l-4.14-0.66l-0.59-0.09l-1.35-0.22c-0.59,0-1.87,0.26-3.22,0.51c-0.71,0.13-1.43,0.27-2.08,0.36 c-0.08,0.01-0.16,0.02-0.23,0.03h-0.01c-0.7-0.15-1.38-0.38-2.02-0.68c-0.2-0.09-0.4-0.19-0.6-0.3c-0.56-0.31-1.1-0.68-1.59-1.09 c-0.01-0.12-0.02-0.22-0.02-0.27c0-0.26,0.01-0.51,0.03-0.76c0.04-0.64,0.13-1.26,0.27-1.86c0.22-0.91,0.54-1.79,0.97-2.6 c0.08-0.17,0.17-0.34,0.27-0.5c0.04-0.08,0.09-0.15,0.13-0.23c0.18-0.29,0.38-0.57,0.58-0.85c0.42-0.55,0.89-1.07,1.39-1.54 c0.01,0,0.01,0,0.01,0c0.04-0.04,0.08-0.08,0.12-0.11c0.05-0.04,0.09-0.09,0.14-0.12c0.2-0.18,0.4-0.34,0.61-0.49 c0-0.01,0.01-0.01,0.01-0.01c1.89-1.41,4.23-2.24,6.78-2.24c1.98,0,3.82,0.5,5.43,1.38h0.01c1.38,0.76,2.58,1.79,3.53,3.03 c0.37,0.48,0.7,0.99,0.98,1.53h0.01c0.05,0.1,0.1,0.2,0.15,0.3c0.3,0.59,0.54,1.21,0.72,1.85h0.01c0.01,0.05,0.03,0.1,0.04,0.15 c0.12,0.43,0.22,0.87,0.29,1.32c0.01,0.09,0.02,0.19,0.03,0.28C32.19,38.43,32.22,38.92,32.22,39.42z"></path>
                                <path fill="#ffd54f" d="M30.17,39.31c0,0.16,0,0.33-0.02,0.49v0.01c0,0.01,0,0.01,0,0.01c-0.02,0.72-0.12,1.43-0.28,2.07 c0,0.04-0.01,0.07-0.03,0.11h-4.67l-3.85-0.83l-0.51-0.11l-0.08,0.02l-4.27,0.88L16.27,42H16c-0.64,0-1.27-0.06-1.88-0.18 c-0.09-0.02-0.18-0.04-0.27-0.06h-0.01c-0.7-0.15-1.38-0.38-2.02-0.68c-0.02-0.11-0.04-0.22-0.05-0.33 c-0.07-0.43-0.1-0.88-0.1-1.33c0-0.17,0-0.34,0.01-0.51c0.03-0.54,0.11-1.07,0.23-1.58c0.08-0.38,0.19-0.75,0.32-1.1 c0.11-0.31,0.24-0.61,0.38-0.9c0.12-0.25,0.26-0.49,0.4-0.73c0.14-0.23,0.29-0.45,0.45-0.67c0.4-0.55,0.87-1.06,1.39-1.51 c0.3-0.26,0.63-0.51,0.97-0.73c1.46-0.96,3.21-1.52,5.1-1.52c0.37,0,0.73,0.02,1.08,0.07h0.02c1.07,0.12,2.07,0.42,2.99,0.87 c0.01,0,0.01,0,0.01,0c1.45,0.71,2.68,1.78,3.58,3.1c0.15,0.22,0.3,0.46,0.43,0.7c0.11,0.19,0.21,0.39,0.3,0.59 c0.14,0.31,0.27,0.64,0.38,0.97h0.01c0.11,0.37,0.21,0.74,0.28,1.13v0.01C30.11,38.16,30.17,38.73,30.17,39.31z"></path>
                                <path fill="#ffe082" d="M28.11,39.52v0.03c0,0.59-0.07,1.17-0.21,1.74c-0.05,0.24-0.12,0.48-0.21,0.71h-4.48l-2.29-0.63L18.63,42H16 c-0.64,0-1.27-0.06-1.88-0.18c-0.02-0.03-0.03-0.06-0.04-0.09c-0.14-0.43-0.25-0.86-0.3-1.31c-0.04-0.29-0.06-0.59-0.06-0.9 c0-0.12,0-0.25,0.02-0.37c0.01-0.47,0.08-0.93,0.2-1.37c0.06-0.3,0.15-0.59,0.27-0.87c0.04-0.14,0.1-0.27,0.17-0.4 c0.15-0.34,0.33-0.67,0.53-0.99c0.22-0.32,0.46-0.62,0.73-0.9c0.32-0.36,0.68-0.69,1.09-0.96c0.7-0.51,1.5-0.89,2.37-1.1 c0.58-0.16,1.19-0.24,1.82-0.24c2,0,3.79,0.8,5.09,2.09c0.05,0.05,0.11,0.11,0.16,0.18h0.01c0.14,0.15,0.27,0.3,0.4,0.47 c0.37,0.47,0.68,0.98,0.92,1.54c0.12,0.26,0.22,0.53,0.3,0.81c0.01,0.04,0.02,0.07,0.03,0.11c0.14,0.49,0.23,1,0.25,1.53 C28.1,39.2,28.11,39.36,28.11,39.52z"></path>
                                <path fill="#ffecb3" d="M26.06,39.52c0,0.41-0.05,0.8-0.16,1.17c-0.1,0.4-0.25,0.78-0.44,1.14c-0.03,0.06-0.1,0.17-0.1,0.17h-8.88 c-0.01-0.01-0.02-0.03-0.02-0.04c-0.12-0.19-0.22-0.38-0.3-0.59c-0.2-0.46-0.32-0.96-0.36-1.48c-0.02-0.12-0.02-0.25-0.02-0.37 c0-0.06,0-0.13,0.01-0.19c0.01-0.44,0.07-0.86,0.19-1.25c0.1-0.36,0.23-0.69,0.4-1.01c0,0,0.01-0.01,0.01-0.02 c0.12-0.21,0.25-0.42,0.4-0.62c0.49-0.66,1.14-1.2,1.89-1.55c0.01,0,0.01,0,0.01,0c0.24-0.12,0.49-0.22,0.75-0.29c0,0,0,0,0.01,0 c0.46-0.14,0.96-0.21,1.47-0.21c0.59,0,1.16,0.09,1.68,0.28c0.19,0.05,0.37,0.13,0.55,0.22c0,0,0,0,0.01,0 c0.86,0.41,1.59,1.05,2.09,1.85c0.1,0.15,0.19,0.31,0.27,0.48c0.04,0.07,0.08,0.15,0.11,0.22c0.23,0.52,0.37,1.09,0.41,1.69 c0.01,0.05,0.01,0.1,0.01,0.16C26.06,39.36,26.06,39.44,26.06,39.52z"></path>
                                <g>
                                <path fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="2" d="M30,11H18c-3.9,0-7,3.1-7,7v12c0,3.9,3.1,7,7,7h12c3.9,0,7-3.1,7-7V18C37,14.1,33.9,11,30,11z"></path>
                                <circle cx="31" cy="16" r="1" fill="#fff"></circle>
                                </g>
                                <g>
                                <circle cx="24" cy="24" r="6" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="2"></circle>
                                </g>
                            </svg>
                            </a>

                            <a href="https://wa.me/6281332683338?text=Kiat%20Surabaya" target="_blank" class="grid h-9 w-9 place-items-center rounded-full border border-white/15 text-pearl/70 transition hover:border-gold hover:text-gold">
                            <!-- SVG WhatsApp -->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="h-5 w-5">
                                <path fill="#fff" d="M4.868,43.303l2.694-9.835C5.9,30.59,5.026,27.324,5.027,23.979C5.032,13.514,13.548,5,24.014,5c5.079,0.002,9.845,1.979,13.43,5.566c3.584,3.588,5.558,8.356,5.556,13.428c-0.004,10.465-8.522,18.98-18.986,18.98c-0.001,0,0,0,0,0h-0.008c-3.177-0.001-6.3-0.798-9.073-2.311L4.868,43.303z"></path>
                                <path fill="#fff" d="M4.868,43.803c-0.132,0-0.26-0.052-0.355-0.148c-0.125-0.127-0.174-0.312-0.127-0.483l2.639-9.636c-1.636-2.906-2.499-6.206-2.497-9.556C4.532,13.238,13.273,4.5,24.014,4.5c5.21,0.002,10.105,2.031,13.784,5.713c3.679,3.683,5.704,8.577,5.702,13.781c-0.004,10.741-8.746,19.48-19.486,19.48c-3.189-0.001-6.344-0.788-9.144-2.277l-9.875,2.589C4.953,43.798,4.911,43.803,4.868,43.803z"></path>
                                <path fill="#cfd8dc" d="M24.014,5c5.079,0.002,9.845,1.979,13.43,5.566c3.584,3.588,5.558,8.356,5.556,13.428c-0.004,10.465-8.522,18.98-18.986,18.98h-0.008c-3.177-0.001-6.3-0.798-9.073-2.311L4.868,43.303l2.694-9.835C5.9,30.59,5.026,27.324,5.027,23.979C5.032,13.514,13.548,5,24.014,5 M24.014,42.974C24.014,42.974,24.014,42.974,24.014,42.974C24.014,42.974,24.014,42.974,24.014,42.974 M24.014,42.974C24.014,42.974,24.014,42.974,24.014,42.974C24.014,42.974,24.014,42.974,24.014,42.974 M24.014,4C24.014,4,24.014,4,24.014,4C12.998,4,4.032,12.962,4.027,23.979c-0.001,3.367,0.849,6.685,2.461,9.622l-2.585,9.439c-0.094,0.345,0.002,0.713,0.254,0.967c0.19,0.192,0.447,0.297,0.711,0.297c0.085,0,0.17-0.011,0.254-0.033l9.687-2.54c2.828,1.468,5.998,2.243,9.197,2.244c11.024,0,19.99-8.963,19.995-19.98c0.002-5.339-2.075-10.359-5.848-14.135C34.378,6.083,29.357,4.002,24.014,4L24.014,4z"></path>
                                <path fill="#40c351" d="M35.176,12.832c-2.98-2.982-6.941-4.625-11.157-4.626c-8.704,0-15.783,7.076-15.787,15.774c-0.001,2.981,0.833,5.883,2.413,8.396l0.376,0.597l-1.595,5.821l5.973-1.566l0.577,0.342c2.422,1.438,5.2,2.198,8.032,2.199h0.006c8.698,0,15.777-7.077,15.78-15.776C39.795,19.778,38.156,15.814,35.176,12.832z"></path>
                                <path fill="#fff" fill-rule="evenodd" d="M19.268,16.045c-0.355-0.79-0.729-0.806-1.068-0.82c-0.277-0.012-0.593-0.011-0.909-0.011c-0.316,0-0.83,0.119-1.265,0.594c-0.435,0.475-1.661,1.622-1.661,3.956c0,2.334,1.7,4.59,1.937,4.906c0.237,0.316,3.282,5.259,8.104,7.161c4.007,1.58,4.823,1.266,5.693,1.187c0.87-0.079,2.807-1.147,3.202-2.255c0.395-1.108,0.395-2.057,0.277-2.255c-0.119-0.198-0.435-0.316-0.909-0.554s-2.807-1.385-3.242-1.543c-0.435-0.158-0.751-0.237-1.068,0.238c-0.316,0.474-1.225,1.543-1.502,1.859c-0.277,0.317-0.554,0.357-1.028,0.119c-0.474-0.238-2.002-0.738-3.815-2.354c-1.41-1.257-2.362-2.81-2.639-3.285c-0.277-0.474-0.03-0.731,0.208-0.968c0.213-0.213,0.474-0.554,0.712-0.831c0.237-0.277,0.316-0.475,0.474-0.791c0.158-0.317,0.079-0.594-0.04-0.831C20.612,19.329,19.69,16.983,19.268,16.045z" clip-rule="evenodd"></path>
                            </svg>
                            </a>
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
          <li class="flex items-center gap-2"><i data-lucide="map-pin" class="h-4 w-4 text-gold"></i>Lebak Permai Utara 3/19A</li>
          <li class="flex items-center gap-2"><i data-lucide="phone" class="h-4 w-4 text-gold"></i>+62 813 3268 3338</li>
          <li class="flex items-center gap-2"><i data-lucide="mail" class="h-4 w-4 text-gold"></i>kiatsurabaya@gmail.com</li>
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
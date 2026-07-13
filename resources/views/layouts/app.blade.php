<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'KIAT — Karya Inti Alam Tunggal | Seafood Premium')</title>
    <meta name="description" content="Seafood segar premium, higienis, dan bersertifikat Halal — langsung dari laut Nusantara ke dapurmu." />

    {{-- ============================================================= --}}
    {{-- KERANGKA TEMA UTAMA (Abyss / Lagoon / Pearl / Gold)           --}}
    {{-- Diekstrak dari user/dashboardUser.blade.php sebagai sumber    --}}
    {{-- desain, kini dipusatkan di layout master agar seluruh halaman --}}
    {{-- yang memakai @extends('layouts.app') tampil konsisten.        --}}
    {{-- ============================================================= --}}

    {{-- Google Fonts: Fraunces (display), Inter (body), JetBrains Mono (harga/utility) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500;1,9..144,600&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet" />

    {{-- Tailwind CDN + konfigurasi palet & font tema baru --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        abyss:  '#071726',   // deep marine navy — navbar & footer
                        marine: '#0B3E56',   // gradasi tengah
                        lagoon: '#16808A',   // aksen teal UI (label, ikon, link)
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

    {{-- Lucide Icons — dipakai navbar, footer & halaman konten --}}
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.js"></script>

    <style>
        html { scroll-behavior: smooth; }
        body { -webkit-font-smoothing: antialiased; }

        :focus-visible { outline: 2px solid #D4AF37; outline-offset: 3px; border-radius: 4px; }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation-duration: 0.001ms !important; animation-iteration-count: 1 !important; transition-duration: 0.001ms !important; scroll-behavior: auto !important; } }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Efek kilau pada tombol CTA emas (aksen premium tema baru) */
        @keyframes shineSweep { 0% { transform: translateX(-140%) skewX(-18deg); } 100% { transform: translateX(260%) skewX(-18deg); } }
        .btn-shine { position: relative; overflow: hidden; }
        .btn-shine .shine { position: absolute; top: 0; left: -30%; width: 22%; height: 100%; background: rgba(255,255,255,0.4); transform: skewX(-18deg); pointer-events: none; }
        .btn-shine:hover .shine { animation: shineSweep .85s ease forwards; }
    </style>

    @stack('styles')
</head>

{{-- Konten disesuaikan dengan Tema Abyss/Pearl: dasar pearl, teks ink, font Inter --}}
<body class="bg-pearl font-sans text-ink antialiased min-h-screen flex flex-col">

    @include('partials.header')

    <main class="flex-1">
        @yield('content')
    </main>

    {{-- ============================================================= --}}
    {{-- FOOTER (Tema Abyss) — dipusatkan dari dashboardUser            --}}
    {{-- ============================================================= --}}
    <footer class="bg-abyss pt-16 mt-auto">
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
                        <li><a href="{{ route('cart.index') }}" class="hover:text-gold">Keranjang</a></li>
                        <li><a href="{{ route('user.dashboard') }}" class="hover:text-gold">Beranda</a></li>
                    </ul>
                </div>
                <div>
                    <p class="font-mono text-xs font-semibold uppercase tracking-[0.16em] text-pearl/40">Perusahaan</p>
                    <ul class="mt-4 space-y-3 text-sm text-pearl/70">
                        <li><a href="{{ route('tentang.kami') }}" class="hover:text-gold">Tentang Kami</a></li>
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
                <p>&copy; {{ date('Y') }} Karya Inti Alam Tunggal (KIAT). Seluruh hak cipta dilindungi.</p>
                <p class="flex items-center gap-2">Higienis <span class="text-white/20">·</span> Halal MUI <span class="text-white/20">·</span> Fresh Guarantee</p>
            </div>
        </div>
    </footer>

    {{-- Inisialisasi ikon Lucide untuk seluruh halaman --}}
    <script>
        (function renderIcons() {
            try { if (window.lucide && typeof window.lucide.createIcons === 'function') { window.lucide.createIcons(); } }
            catch (err) { console.warn('Lucide gagal dimuat:', err); }
        })();
    </script>

    @stack('scripts')

</body>
</html>

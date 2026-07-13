@extends('layouts.app')

@section('title', 'Tentang Kami - KIAT Frozen Food')

@section('content')

{{-- Konten disesuaikan dengan Tema Abyss/Pearl/Gold: judul Fraunces, kartu bg-white/90, --}}
{{-- aksen biru lama diganti lagoon/gold, kartu fakta memakai dasar abyss premium. --}}
<main class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8 font-sans">

    {{-- 1. HERO HEADER --}}
    <div class="text-center mb-16">
        <p class="font-mono text-xs font-semibold uppercase tracking-[0.2em] text-lagoon mb-3">Karya Inti Alam Tunggal</p>
        <h1 class="font-display text-4xl md:text-5xl font-semibold text-ink mb-4 tracking-tight">
            KIAT <span class="italic text-gold">Frozen Food</span>
        </h1>
        <p class="text-xl text-ink/60 max-w-2xl mx-auto">
            Mengenal lebih dekat UD. Karya Inti Alam Tunggal, mitra terpercaya kebutuhan frozen food Anda sejak tahun 2000.
        </p>
    </div>

    {{-- 2. KONTEN UTAMA (GRID) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">

        {{-- KOLOM KIRI: CERITA & FILOSOFI --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- Kartu Filosofi --}}
            <div class="bg-white/90 p-8 rounded-2xl shadow-sm border border-ink/5">
                <div class="flex items-center gap-3 mb-4">
                    <span class="p-2 bg-lagoon/10 text-lagoon rounded-lg text-xl">💡</span>
                    <h2 class="font-display text-2xl font-semibold text-ink">Filosofi Kami</h2>
                </div>
                <p class="text-ink/70 leading-relaxed text-lg">
                    <strong class="text-ink">UD. Karya Inti Alam Tunggal</strong> adalah perusahaan distribusi frozen food yang telah berdiri kokoh sejak tahun 2000. Komitmen utama kami adalah menjaga kualitas dan kesegaran produk melalui penerapan rantai dingin yang ketat dan disiplin.
                </p>
            </div>

            {{-- Kartu Jangkauan --}}
            <div class="bg-white/90 p-8 rounded-2xl shadow-sm border border-ink/5">
                <div class="flex items-center gap-3 mb-4">
                    <span class="p-2 bg-lagoon/10 text-lagoon rounded-lg text-xl">🚚</span>
                    <h2 class="font-display text-2xl font-semibold text-ink">Jangkauan Distribusi</h2>
                </div>
                <p class="text-ink/70 leading-relaxed">
                    Kami melayani distribusi untuk wilayah <span class="font-bold text-ink">Yogyakarta, Solo, Sragen</span>, hingga pengiriman ke <span class="font-bold text-ink">Luar Pulau Jawa</span>. Armada kami dilengkapi pendingin standar industri untuk memastikan produk sampai di tangan Anda dalam kondisi beku sempurna.
                </p>
            </div>

        </div>

        {{-- Kartu Fakta Singkat: dasar abyss premium dengan aksen gold --}}
        <div class="lg:col-span-1 space-y-6">

            <div class="bg-abyss p-8 rounded-2xl shadow-lg text-pearl">
                <h3 class="font-display text-xl font-semibold mb-6 border-b border-white/10 pb-2">Fakta Singkat</h3>

                <div class="space-y-6">
                    <div>
                        <p class="text-pearl/50 text-xs uppercase font-bold tracking-wider">Tahun Berdiri</p>
                        <p class="font-display text-3xl font-semibold mt-1 text-gold">2000</p>
                    </div>
                    <div>
                        <p class="text-pearl/50 text-xs uppercase font-bold tracking-wider">Basis Operasional</p>
                        <p class="text-xl font-bold mt-1">Jawa Timur, ID</p>
                    </div>
                    <div>
                        <p class="text-pearl/50 text-xs uppercase font-bold tracking-wider">Fokus Utama</p>
                        <p class="text-lg font-bold mt-1">Seafood &amp; Frozen Food</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- 3. PRODUK UNGGULAN (LIST) --}}
    <div class="bg-white/90 rounded-3xl p-8 md:p-12 shadow-sm border border-ink/5 text-center">
        <h2 class="font-display text-3xl font-semibold text-ink mb-8">Produk Unggulan Kami</h2>

        <div class="flex flex-wrap justify-center gap-3">
            @foreach ([
                'Fillet Ikan Dori',
                'Cumi Tube',
                'Fillet Kakap',
                'Daging Ikan Tenggiri',
                'Crab Meat',
                'Udang Kupas',
                'Scallop',
                'Produk Olahan Laut'
            ] as $produk)
                <span class="px-5 py-2.5 bg-lagoon/10 text-lagoon rounded-full font-semibold border border-lagoon/20 hover:bg-lagoon/20 hover:border-lagoon/30 transition cursor-default">
                    {{ $produk }}
                </span>
            @endforeach
        </div>

        <div class="mt-10">
            {{-- CTA emas (gold) sesuai tema premium --}}
            <a href="{{ route('produk.kategori') }}" class="btn-shine inline-flex items-center justify-center px-8 py-3 text-base font-semibold rounded-full text-abyss bg-gold shadow-glow hover:scale-[1.03] md:py-4 md:text-lg md:px-10 transition-transform">
                <span class="shine"></span>
                Lihat Katalog Lengkap →
            </a>
        </div>
    </div>

</main>

@endsection
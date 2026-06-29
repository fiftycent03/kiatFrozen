@extends('layouts.app')

@section('title', 'Tentang Kami - KIAT Frozen Food')

@section('content')

<main class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8 font-sans">

    {{-- 1. HERO HEADER --}}
    <div class="text-center mb-16">
        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4 tracking-tight">
            KIAT <span class="text-blue-600">Frozen Food</span>
        </h1>
        <p class="text-xl text-gray-500 max-w-2xl mx-auto">
            Mengenal lebih dekat UD. Karya Inti Alam Tunggal, mitra terpercaya kebutuhan frozen food Anda sejak tahun 2000.
        </p>
    </div>

    {{-- 2. KONTEN UTAMA (GRID) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">

        {{-- KOLOM KIRI: CERITA & FILOSOFI --}}
        <div class="lg:col-span-2 space-y-8">
            
            {{-- Kartu Filosofi --}}
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-4">
                    <span class="p-2 bg-blue-100 text-blue-600 rounded-lg text-xl">💡</span>
                    <h2 class="text-2xl font-bold text-gray-800">Filosofi Kami</h2>
                </div>
                <p class="text-gray-600 leading-relaxed text-lg">
                    <strong class="text-gray-800">UD. Karya Inti Alam Tunggal</strong> adalah perusahaan distribusi frozen food yang telah berdiri kokoh sejak tahun 2000. Komitmen utama kami adalah menjaga kualitas dan kesegaran produk melalui penerapan rantai dingin yang ketat dan disiplin.
                </p>
            </div>

            {{-- Kartu Jangkauan --}}
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-4">
                    <span class="p-2 bg-green-100 text-green-600 rounded-lg text-xl">🚚</span>
                    <h2 class="text-2xl font-bold text-gray-800">Jangkauan Distribusi</h2>
                </div>
                <p class="text-gray-600 leading-relaxed">
                    Kami melayani distribusi untuk wilayah <span class="font-bold text-gray-800">Yogyakarta, Solo, Sragen</span>, hingga pengiriman ke <span class="font-bold text-gray-800">Luar Pulau Jawa</span>. Armada kami dilengkapi pendingin standar industri untuk memastikan produk sampai di tangan Anda dalam kondisi beku sempurna.
                </p>
            </div>

        </div>

        <div class="lg:col-span-1 space-y-6">
            
            <div class="bg-blue-600 p-8 rounded-2xl shadow-lg text-white">
                <h3 class="text-xl font-bold mb-6 border-b border-blue-400 pb-2">Fakta Singkat</h3>
                
                <div class="space-y-6">
                    <div>
                        <p class="text-blue-200 text-xs uppercase font-bold tracking-wider">Tahun Berdiri</p>
                        <p class="text-3xl font-extrabold mt-1">2000</p>
                    </div>
                    <div>
                        <p class="text-blue-200 text-xs uppercase font-bold tracking-wider">Basis Operasional</p>
                        <p class="text-xl font-bold mt-1">Jawa Timur, ID</p>
                    </div>
                    <div>
                        <p class="text-blue-200 text-xs uppercase font-bold tracking-wider">Fokus Utama</p>
                        <p class="text-lg font-bold mt-1">Seafood & Frozen Food</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- 3. PRODUK UNGGULAN (LIST) --}}
    <div class="bg-white rounded-3xl p-8 md:p-12 shadow-sm border border-gray-100 text-center">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">Produk Unggulan Kami</h2>
        
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
                <span class="px-5 py-2.5 bg-blue-50 text-blue-700 rounded-full font-semibold border border-blue-100 hover:bg-blue-100 hover:border-blue-200 transition cursor-default">
                    {{ $produk }}
                </span>
            @endforeach
        </div>

        <div class="mt-10">
            <a href="{{ route('produk.kategori') }}" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-bold rounded-full text-white bg-blue-600 hover:bg-blue-700 md:py-4 md:text-lg md:px-10 transition shadow-lg shadow-blue-200">
                Lihat Katalog Lengkap →
            </a>
        </div>
    </div>

</main>

@endsection
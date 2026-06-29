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

    {{-- KATEGORI FAVORIT --}}
    <div class="mb-16">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Kategori Favorit</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @php
                $cats = [
                    ['name' => 'Cumi-Cumi', 'icon' => '🦑', 'slug' => 'cumi'],
                    ['name' => 'Ikan Dory', 'icon' => '🐟', 'slug' => 'dory'],
                    ['name' => 'Udang', 'icon' => '🦐', 'slug' => 'udang'],
                    ['name' => 'Kepiting', 'icon' => '🦀', 'slug' => 'kepiting'],
                ];
            @endphp

            @foreach($cats as $cat)
            <a href="/produk/{{ $cat['slug'] }}" class="group block bg-white border border-gray-100 rounded-2xl p-6 text-center shadow-sm hover:shadow-md hover:border-blue-400 transition">
                <div class="text-5xl mb-3 group-hover:scale-110 transition transform">{{ $cat['icon'] }}</div>
                <h3 class="font-bold text-gray-700 group-hover:text-blue-600">{{ $cat['name'] }}</h3>
            </a>
            @endforeach
        </div>
    </div>

    {{-- PRODUK PILIHAN --}}
    <div>
        <div class="flex justify-between items-end mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Beberapa Produk Kami</h2>
                <p class="text-gray-500 text-sm mt-1">Pilihan produk berkualitas tinggi untuk Anda</p>
            </div>
            <a href="{{ route('produk.kategori') }}" class="text-blue-600 font-bold hover:underline text-sm flex items-center gap-1">
                Lihat Semua Katalog <span>→</span>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            @forelse($products as $product)
            {{-- Card diubah menjadi Link ke Katalog dengan filter pencarian --}}
            <a href="{{ route('produk.kategori') }}?search={{ urlencode($product->name) }}" 
               class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col h-full border-b-4 hover:border-blue-500">
                
                {{-- Image --}}
                <div class="relative h-48 bg-gray-100 overflow-hidden">
                   <img src="{{ $product->primaryImage ? '/storage/' . $product->primaryImage->path : 'https://placehold.co/400x300?text=No+Image' }}" 
     class="w-full h-full object-cover group-hover:scale-110 transition duration-700"
     alt="{{ $product->name }}">
                    
                    <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-[10px] font-bold text-blue-600 shadow-sm uppercase tracking-wider">
                        Detail Produk
                    </div>
                </div>

                {{-- Detail --}}
                <div class="p-5 flex flex-col flex-1">
                    <h3 class="font-bold text-gray-800 text-lg leading-snug mb-2 group-hover:text-blue-600 transition">
                        {{ $product->name }}
                    </h3>
                    
                    <p class="text-gray-500 text-xs line-clamp-2 mb-4 flex-1">
                        {{ $product->description ?? 'Nikmati kesegaran seafood kualitas premium yang diolah dengan standar higienis tinggi.' }}
                    </p>
                    
                    <div class="pt-4 border-t border-gray-50 mt-auto">
                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest block mb-1">Harga per Kg</span>
                        <div class="flex items-center justify-between">
                            <div class="text-blue-600 font-black text-xl">
                                Rp {{ number_format($product->price_per_kg, 0, ',', '.') }}
                            </div>
                            <div class="bg-blue-50 text-blue-600 p-2 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            @empty
            <div class="col-span-full text-center py-20 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
                <div class="text-4xl mb-2">🧊</div>
                <p class="text-gray-500 font-medium italic">Belum ada produk unggulan yang ditampilkan.</p>
            </div>
            @endforelse
        </div>
    </div>

</div>

@endsection
@extends('layouts.app')

@section('title', 'Katalog Produk | KIAT Frozen Food')

@section('content')

{{-- Memasukkan Alpine.js via CDN jika belum ada di layout utama --}}
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
    /* Menghilangkan spinner input number */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    input[type=number] { -moz-appearance: textfield; }
    [x-cloak] { display: none !important; }
</style>

<main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8 font-sans">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Katalog Produk</h1>
        <p class="text-gray-500 mt-1">
            Menampilkan produk: <span class="font-bold text-blue-600">{{ $kategori ? ucfirst($kategori) : 'Semua Produk' }}</span>
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        
        {{-- SIDEBAR --}}
        <aside class="space-y-6">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-700 mb-3 text-sm uppercase tracking-wider">Cari Produk</h3>
                <form method="GET" action="{{ route('produk.kategori', $kategori) }}">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ikan..." 
                               class="w-full pl-4 pr-10 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm transition">
                        <button type="submit" class="absolute right-3 top-3 text-gray-400">🔍</button>
                    </div>
                </form>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-700 mb-3 text-sm uppercase tracking-wider">Kategori</h3>
                <nav class="space-y-1">
                    <a href="{{ route('produk.kategori') }}" class="block px-3 py-2 rounded-lg text-sm {{ !$kategori ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-600 hover:bg-gray-50' }}">📂 Semua Produk</a>
                    @foreach(['cumi','dory','fillet ikan','kepiting','scallop','udang'] as $kat)
                        <a href="{{ route('produk.kategori', $kat) }}" class="block px-3 py-2 rounded-lg text-sm {{ $kategori == $kat ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-600 hover:bg-gray-50' }}">🔹 {{ ucfirst($kat) }}</a>
                    @endforeach
                </nav>
            </div>
        </aside>

        {{-- GRID PRODUK --}}
        <div class="md:col-span-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                @forelse($products as $product)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition flex flex-col h-full"
                     x-data="{ 
                        qty: {{ $product->stock > 0 ? ($product->min_pembelian ?? 1) : 0 }}, 
                        min: {{ $product->min_pembelian ?? 1 }},
                        stock: {{ $product->stock }}
                     }">
                    
                    {{-- IMAGE SECTION --}}
                    <div class="relative h-48 bg-gray-100">
                        @if($product->primaryImage)
                            <img src="{{ asset('storage/'.$product->primaryImage->path) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs italic text-center p-4">Gambar belum tersedia</div>
                        @endif
                        
                        <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-2 py-1 rounded text-[10px] font-bold text-gray-600 shadow-sm uppercase">
                            {{ $product->satuan ?? 'Kg' }}
                        </div>

                        <div class="absolute bottom-3 left-3">
                            @if($product->stock > 0)
                                <span class="bg-blue-600 text-white text-[10px] px-2 py-1 rounded-full font-bold shadow-lg">Stok: {{ $product->stock }}</span>
                            @else
                                <span class="bg-red-500 text-white text-[10px] px-2 py-1 rounded-full font-bold shadow-lg">HABIS</span>
                            @endif
                        </div>
                    </div>

                    {{-- DETAIL SECTION --}}
                    <div class="p-5 flex flex-col flex-1 {{ $product->stock <= 0 ? 'opacity-60' : '' }}">
                        <div class="mb-4 flex-1">
                            <h3 class="font-bold text-gray-800 text-lg leading-tight">{{ $product->name }}</h3>
                            
                            {{-- Info Minimal Pembelian --}}
                            <div class="inline-flex items-center gap-1.5 text-orange-600 bg-orange-50 px-2 py-1 rounded-md text-[10px] font-bold uppercase mt-2 border border-orange-100">
                                📌 Min. Beli: {{ $product->min_pembelian }} {{ $product->satuan }}
                            </div>

                            <div class="text-blue-600 font-black text-xl mt-2">
                                Rp {{ number_format($product->price_per_kg, 0, ',', '.') }}
                            </div>
                        </div>

                        {{-- ACTION FORM --}}
                        <form action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            {{-- QUANTITY BUTTONS --}}
                            <div class="flex items-center justify-between bg-gray-50 rounded-xl p-1.5 border border-gray-200 mb-4">
                                {{-- Tombol Kurangi --}}
                                <button type="button" 
                                        @click="if(qty > min) qty--"
                                        class="w-10 h-10 flex items-center justify-center bg-white rounded-lg text-gray-600 shadow-sm transition active:scale-95"
                                        :class="qty <= min ? 'opacity-30 cursor-not-allowed' : 'hover:bg-red-50 hover:text-red-500'"
                                        :disabled="stock <= 0">
                                    <span class="text-xl font-bold">-</span>
                                </button>
                                
                                {{-- Angka Quantity (Input) --}}
                                <input type="number" 
                                       name="qty" 
                                       x-model.number="qty"
                                       class="w-14 text-center bg-transparent border-none focus:ring-0 font-black text-gray-800 text-lg" 
                                       readonly>
                                
                                {{-- Tombol Tambah --}}
                                <button type="button" 
                                        @click="if(qty < stock) qty++"
                                        class="w-10 h-10 flex items-center justify-center bg-white rounded-lg text-gray-600 shadow-sm transition active:scale-95"
                                        :class="qty >= stock ? 'opacity-30 cursor-not-allowed' : 'hover:bg-blue-50 hover:text-blue-500'"
                                        :disabled="stock <= 0">
                                    <span class="text-xl font-bold">+</span>
                                </button>
                            </div>

                            <div class="flex flex-col gap-2">
                                @if($product->stock > 0)
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl text-sm font-bold transition shadow-lg shadow-blue-100 active:scale-95 flex items-center justify-center gap-2">
                                        🛒 + Keranjang
                                    </button>
                                    <button type="submit" formaction="{{ route('cart.buyNow') }}" class="w-full bg-gray-800 hover:bg-gray-900 text-white py-2 rounded-lg text-xs font-bold transition opacity-80 hover:opacity-100">
                                        Beli Langsung
                                    </button>
                                @else
                                    <button type="button" class="w-full bg-gray-200 text-gray-400 py-3 rounded-xl text-sm font-bold cursor-not-allowed" disabled>
                                        Stok Habis
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
                @empty
                    <div class="col-span-full py-20 text-center bg-white rounded-3xl border-2 border-dashed border-gray-200">
                        <p class="text-gray-400 italic">Produk tidak ditemukan...</p>
                    </div>
                @endforelse

            </div>
        </div>
    </div>
</main>

@endsection
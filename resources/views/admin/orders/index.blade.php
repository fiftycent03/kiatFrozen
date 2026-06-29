@extends('layouts.admin')

@section('content')
<div x-data="{ showModal: false, imgUrl: '', openModal(url) { this.imgUrl = url; this.showModal = true; } }">

    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Pesanan</h2>
            <p class="text-gray-500 text-sm">Konfirmasi pembayaran dan kirim barang via Kurir Pribadi.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl flex items-center gap-2">
        <i data-lucide="check-circle" class="w-5 h-5"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    @endif

    <div class="bg-white rounded-2xl card-shadow border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-gray-600 text-xs uppercase tracking-wider border-b border-slate-100">
                        <th class="p-4 font-bold">Info Order</th>
                        <th class="p-4 font-bold">Customer & Alamat</th>
                        <th class="p-4 font-bold">Tagihan & Bukti</th>
                        <th class="p-4 text-center font-bold">Status</th>
                        <th class="p-4 text-center font-bold">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm divide-y divide-slate-100">
                    @forelse($orders as $order)
                    <tr class="hover:bg-slate-50/50 transition align-top">
                        
                        {{-- 1. INFO ORDER --}}
                        <td class="p-4">
                            <span class="block font-bold text-blue-600 text-base tracking-tight">{{ $order->code }}</span>
                            <span class="text-[10px] text-gray-400 block uppercase font-bold mt-0.5">{{ $order->created_at->format('d M Y, H:i') }}</span>
                            <div class="mt-2">
                                @if(strtolower($order->payment_channel) == 'cod')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-yellow-100 text-yellow-800 border border-yellow-200 uppercase tracking-wider">📦 COD</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider">💳 Transfer</span>
                                @endif
                            </div>
                        </td>

                        {{-- 2. CUSTOMER --}}
                        <td class="p-4">
                            <div class="font-bold text-gray-800 text-sm">{{ $order->customer_name }}</div>
                            <div class="text-xs text-gray-500 leading-relaxed w-64 mt-1">
                                {{ $order->customer_address }}, {{ $order->province }}
                            </div>
                            <div class="text-[10px] text-blue-500 font-bold mt-1 uppercase italic">
                                Jasa: {{ $order->shipping_service }}
                            </div>
                        </td>

                        {{-- 3. TAGIHAN & BUKTI --}}
                        <td class="p-4">
                            <div class="font-bold text-lg text-gray-900 tracking-tighter">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
                            @if($order->payment_proof)
                                <button @click="openModal('{{ asset('storage/' . $order->payment_proof) }}')" 
                                        class="mt-1 flex items-center space-x-1 text-[11px] text-blue-600 hover:text-blue-800 font-semibold underline decoration-blue-200">
                                    <i data-lucide="image" class="w-3.5 h-3.5"></i>
                                    <span>Cek Bukti Bayar</span>
                                </button>
                            @endif
                        </td>

                        {{-- 4. STATUS SAAT INI --}}
                        <td class="p-4 text-center align-middle">
                            @if($order->fulfillment_status == 'pending')
                                <span class="text-[10px] font-bold bg-gray-100 text-gray-500 px-2 py-1 rounded-full uppercase tracking-widest italic">Belum Diproses</span>
                            @elseif($order->fulfillment_status == 'shipped')
                                <span class="text-[10px] font-bold bg-blue-100 text-blue-600 px-2 py-1 rounded-full uppercase tracking-widest">🚛 Sedang Diantar</span>
                            @elseif($order->fulfillment_status == 'delivered')
                                <span class="text-[10px] font-bold bg-green-100 text-green-600 px-2 py-1 rounded-full uppercase tracking-widest">✅ Sampai</span>
                            @endif
                        </td>

                        {{-- 5. TOMBOL OTOMATIS (PALING KANAN) --}}
                        <td class="p-4 text-center align-middle">
                            @if($order->fulfillment_status == 'pending')
                                <form action="{{ route('admin.orders.quick-process', $order->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="inline-flex flex-col items-center justify-center w-24 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl transition shadow-lg shadow-blue-200 group active:scale-95">
                                        <i data-lucide="zap" class="w-6 h-6 mb-1 group-hover:text-yellow-300 transition-all"></i>
                                        <span class="text-[10px] font-black uppercase tracking-widest">KIRIM</span>
                                    </button>
                                </form>
                            @else
                                <div class="w-24 mx-auto py-2 flex flex-col items-center justify-center border-2 border-dashed border-gray-100 rounded-2xl opacity-40">
                                    <i data-lucide="lock" class="w-4 h-4 mb-0.5 text-gray-300"></i>
                                    <span class="text-[8px] font-bold uppercase text-gray-400">Locked</span>
                                </div>
                            @endif
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-16 text-center text-gray-400">Belum ada pesanan masuk.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL PREVIEW BUKTI --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center bg-black/80 backdrop-blur-md p-4" x-transition.opacity>
        <div class="relative max-w-2xl w-full" @click.away="showModal = false">
            <button @click="showModal = false" class="absolute -top-12 right-0 text-white hover:text-gray-300 transition transform hover:rotate-90">
                <i data-lucide="x" class="w-10 h-10"></i>
            </button>
            <div class="bg-white p-2 rounded-2xl">
                <img :src="imgUrl" class="w-full rounded-xl object-contain max-h-[80vh]">
            </div>
        </div>
    </div>

</div>

<script>
    lucide.createIcons();
</script>
@endsection
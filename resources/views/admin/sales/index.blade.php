@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Laporan Penjualan</h2>
            <p class="text-sm text-gray-500">Rekapitulasi transaksi yang sudah lunas (Paid).</p>
        </div>
        <button class="mt-4 md:mt-0 bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 shadow-sm flex items-center">
            <i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
            Semua Periode
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-gradient-to-r from-blue-600 to-blue-500 rounded-2xl p-6 text-white shadow-lg shadow-blue-200">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-white/20 p-2 rounded-lg">
                    <i data-lucide="wallet" class="w-6 h-6 text-white"></i>
                </div>
                <span class="text-blue-100 text-sm font-medium">Total Pendapatan</span>
            </div>
            <h3 class="text-4xl font-bold">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            <p class="text-blue-100 text-sm mt-2">Akumulasi dari status 'Paid'</p>
        </div>

        <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-green-50 p-2 rounded-lg">
                    <i data-lucide="shopping-bag" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-gray-500 text-sm font-medium">Transaksi Sukses</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-800">{{ $totalTransactions }} <span class="text-lg text-gray-400 font-normal">Pesanan</span></h3>
            <p class="text-gray-400 text-sm mt-2">Pesanan yang telah dibayar lunas</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="font-bold text-gray-800">Rincian Transaksi Masuk</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-gray-600 text-xs uppercase tracking-wider">
                        <th class="p-4 font-semibold">Tanggal Bayar</th>
                        <th class="p-4 font-semibold">Kode Order</th>
                        <th class="p-4 font-semibold">Pelanggan</th>
                        <th class="p-4 font-semibold">Item</th>
                        <th class="p-4 font-semibold text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-gray-700">
                    @forelse($sales as $item)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-4">
                            <div class="font-medium text-gray-900">
                                {{ $item->paid_at ? \Carbon\Carbon::parse($item->paid_at)->format('d M Y') : '-' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $item->paid_at ? \Carbon\Carbon::parse($item->paid_at)->format('H:i') : '-' }} WIB
                            </div>
                        </td>

                        <td class="p-4">
                            <a href="#" class="text-blue-600 hover:underline font-bold">{{ $item->code }}</a>
                        </td>

                        <td class="p-4">
                            <div class="font-medium">{{ $item->customer_name }}</div>
                            <div class="text-xs text-gray-500">{{ $item->payment_channel }}</div>
                        </td>

                        <td class="p-4">
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">
                                Lihat Detail Order
                            </span>
                        </td>

                        <td class="p-4 text-right font-bold text-gray-800">
                            + Rp {{ number_format($item->total, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-500">
                            Belum ada data penjualan yang lunas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $sales->links() }}
        </div>
    </div>
</div>
@endsection
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KIAT — Dashboard Kurir</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="min-h-screen bg-slate-50">

    {{-- HEADER --}}
    <header class="bg-white border-b border-slate-200 sticky top-0 z-10 shadow-sm">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-2xl">❄️</span>
                <div>
                    <div class="font-extrabold text-blue-600 leading-none">KIAT FROZEN</div>
                    <div class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold">Portal Kurir</div>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600 font-semibold">{{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="GET">
                    @csrf
                    <button class="text-sm text-red-500 hover:text-red-700 font-bold transition">Keluar</button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-8 space-y-8">

        {{-- FLASH MESSAGE --}}
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-5 py-3 rounded-xl font-semibold text-sm flex items-center gap-2">
            ✅ {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-xl font-semibold text-sm">
            ⚠️ {{ session('error') }}
        </div>
        @endif

        {{-- SECTION 1: PESANAN MENUNGGU PENGANTARAN --}}
        <section>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-extrabold text-gray-800">📦 Perlu Diantarkan</h2>
                <span class="bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full">
                    {{ $pendingOrders->count() }} pesanan
                </span>
            </div>

            @forelse($pendingOrders as $order)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-4 overflow-hidden">
                {{-- Info Pesanan --}}
                <div class="p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="space-y-1">
                        <div class="font-extrabold text-gray-800 text-lg">
                            {{ $order->customer_name }}
                            <span class="text-sm font-normal text-gray-400 ml-2">#{{ $order->code }}</span>
                        </div>
                        <div class="text-sm text-gray-500">📞 {{ $order->customer_phone }}</div>
                        <div class="text-sm text-gray-600 font-medium">
                            📍 {{ $order->customer_address }},
                            {{ $order->district }}, {{ $order->city }}, {{ $order->province }}
                        </div>
                        <div class="text-sm font-bold text-blue-600">
                            Total: Rp {{ number_format($order->total, 0, ',', '.') }}
                        </div>
                    </div>
                    {{-- Tombol buka/tutup form upload --}}
                    <button onclick="toggleForm('form-{{ $order->id }}')"
                            class="flex-shrink-0 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition shadow-md shadow-blue-100 whitespace-nowrap">
                        📸 Upload Bukti
                    </button>
                </div>

                {{-- Form Upload Bukti (tersembunyi secara default) --}}
                <div id="form-{{ $order->id }}" class="hidden border-t border-slate-100 bg-slate-50 px-5 py-4">
                    <p class="text-sm font-semibold text-gray-600 mb-3">Foto bukti pengiriman untuk pesanan ini:</p>
                    <form action="{{ route('courier.updateDelivery', $order->id) }}" method="POST"
                          enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        {{-- Toggle sumber foto: Galeri atau Kamera langsung --}}
                        <div class="flex gap-4">
                            <label class="flex items-center gap-1.5 cursor-pointer text-sm font-semibold text-gray-700">
                                <input type="radio" name="source-{{ $order->id }}" value="gallery" checked
                                       onchange="setCapture('{{ $order->id }}', null)"
                                       class="accent-blue-600">
                                🖼️ Pilih dari Galeri
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer text-sm font-semibold text-gray-700">
                                <input type="radio" name="source-{{ $order->id }}" value="camera"
                                       onchange="setCapture('{{ $order->id }}', 'environment')"
                                       class="accent-blue-600">
                                📸 Kamera Langsung
                            </label>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 items-end">
                            <div class="flex-1">
                                {{-- Input foto: capture attribute diubah via JS sesuai pilihan kurir --}}
                                <input type="file" id="proof-{{ $order->id }}" name="delivery_proof"
                                       accept="image/*"
                                       onchange="previewImg(this, 'prev-{{ $order->id }}')"
                                       class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-200 rounded-xl p-2 bg-white cursor-pointer" required>
                                {{-- Preview thumbnail sebelum submit --}}
                                <img id="prev-{{ $order->id }}" src="" alt="" class="hidden mt-3 h-28 w-auto rounded-lg border border-slate-200 object-cover shadow-sm">
                            </div>
                            <button type="submit"
                                    class="flex-shrink-0 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition shadow-md shadow-green-100 whitespace-nowrap"
                                    onclick="return confirm('Konfirmasi upload bukti pengiriman untuk pesanan #{{ $order->code }}?')">
                                ✅ Konfirmasi Terkirim
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-2xl border-2 border-dashed border-slate-200 py-16 text-center">
                <p class="text-3xl mb-2">🎉</p>
                <p class="font-bold text-gray-700">Semua pesanan sudah diantarkan!</p>
                <p class="text-sm text-gray-400 mt-1">Tidak ada pesanan dalam status pengiriman saat ini.</p>
            </div>
            @endforelse
        </section>

        {{-- SECTION 2: SUDAH DIANTARKAN HARI INI --}}
        @if($deliveredToday->isNotEmpty())
        <section>
            <h2 class="text-xl font-extrabold text-gray-800 mb-4">✅ Terkirim Hari Ini</h2>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm divide-y divide-slate-50">
                @foreach($deliveredToday as $order)
                <div class="flex items-center justify-between p-5 hover:bg-slate-50 transition">
                    <div>
                        <p class="font-bold text-gray-800">{{ $order->customer_name }}
                            <span class="text-xs font-normal text-gray-400 ml-2">#{{ $order->code }}</span>
                        </p>
                        <p class="text-sm text-gray-500">{{ $order->city }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-green-600 font-bold">✅ Terkirim</p>
                        <p class="text-xs text-gray-400">
                            {{ \Carbon\Carbon::parse($order->delivered_at)->format('H:i') }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

    </main>

    <script>
        function toggleForm(id) {
            const el = document.getElementById(id);
            el.classList.toggle('hidden');
        }

        // Tampilkan preview foto sebelum diupload.
        function previewImg(input, previewId) {
            const prev = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                prev.src = URL.createObjectURL(input.files[0]);
                prev.classList.remove('hidden');
            }
        }

        // Ubah atribut capture pada input file sesuai pilihan kurir:
        // capture="environment" → buka kamera belakang HP langsung.
        // null → hapus atribut capture → browser buka galeri biasa.
        function setCapture(orderId, capture) {
            const input = document.getElementById('proof-' + orderId);
            if (capture) {
                input.setAttribute('capture', capture);
            } else {
                input.removeAttribute('capture');
            }
            // Reset nilai input agar kurir tidak keliru kirim foto lama.
            input.value = '';
            const prev = document.getElementById('prev-' + orderId);
            if (prev) { prev.classList.add('hidden'); prev.src = ''; }
        }
    </script>
</body>
</html>

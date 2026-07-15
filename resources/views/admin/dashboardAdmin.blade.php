<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KIAT Dashboard Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">

    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        .card-shadow {
            box-shadow: 0 10px 15px -3px rgba(59,130,246,0.1), 0 4px 6px -2px rgba(59,130,246,0.05);
        }
    </style>
</head>

<body class="min-h-screen flex">

    <aside id="sidebar"
           class="sidebar fixed inset-y-0 left-0 transform -translate-x-full md:translate-x-0 md:relative bg-white w-64 p-4 flex flex-col border-r border-slate-100 z-20"
           x-data="{ openDashboard: true }">

        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                <!-- Logo resmi perusahaan (menggantikan ikon kepingan salju + teks "KIAT Dashboard") -->
                <img src="{{ asset('storage/Logo_Kiat.png') }}" alt="Logo Karya Inti Alam Tunggal"
                    class="h-11 w-11 rounded-full object-cover shadow-md border border-white/20" />
                <span class="font-bold text-xl text-blue-800 tracking-tight">Karya Inti Alam Tunggal</span>
            </h1>

            <button id="close-sidebar" class="md:hidden text-gray-500 hover:text-gray-700 p-1">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <nav class="flex-grow space-y-1">
            <div class="mb-1">
                <div class="flex items-center justify-between w-full p-2 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-600' : '' }}">
                    <a href="{{ route('admin.dashboard') }}" class="flex-grow flex items-center cursor-pointer p-1">
                        <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3"></i>
                        <span>Dashboard</span>
                    </a>
                    <button @click="openDashboard = !openDashboard" 
                            class="p-1 rounded-md hover:bg-gray-200 text-gray-400 hover:text-gray-600 transition focus:outline-none">
                        <i :class="openDashboard ? 'rotate-180' : 'rotate-0'" 
                           data-lucide="chevron-down" 
                           class="w-4 h-4 transition-transform duration-200"></i>
                    </button>
                </div>
                <div x-show="openDashboard" x-transition class="mt-1 ml-8 flex flex-col space-y-1 border-l-2 border-slate-100 pl-2">
                    <a href="{{ route('admin.orders.index') }}" class="flex items-center text-gray-600 text-sm p-2 rounded-lg hover:bg-slate-50 hover:text-blue-600 transition">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300 mr-2"></span>
                        Pesanan Baru
                    </a>
                    <a href="{{ route('admin.sales.index') }}" class="flex items-center text-gray-600 text-sm p-2 rounded-lg hover:bg-slate-50 hover:text-blue-600 transition">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300 mr-2"></span>
                        Total Penjualan
                    </a>
                </div>
            </div>
            <a href="{{ route('admin.products.index') }}" class="flex items-center p-3 rounded-xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-medium transition">
                <i data-lucide="package" class="w-5 h-5 mr-3"></i>Produk
            </a>
            <a href="{{ route('admin.users.index') }}" class="flex items-center p-3 rounded-xl text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-medium transition">
                <i data-lucide="users" class="w-5 h-5 mr-3"></i>Pengguna
            </a>
            <form action="{{ route('logout') }}" method="GET" class="mt-auto">
            @csrf
            <button type="submit" class="flex items-center w-full p-3 rounded-xl text-red-500 hover:bg-red-50 font-medium">
                <i data-lucide="log-out" class="w-5 h-5 mr-3"></i>Keluar
            </button>
        </form>
        </nav>

        
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="flex items-center justify-between p-4 bg-white border-b border-slate-100 sticky top-0 z-10">
            <h2 class="text-xl font-semibold text-gray-800">Selamat Datang, Admin!</h2>
            <div class="flex items-center space-x-4">

                {{-- LONCENG NOTIFIKASI: hitung notifikasi belum dibaca dari tabel notifications --}}
                @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open; $nextTick(() => { if(open) markRead() })"
                            class="relative p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-full transition">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        {{-- Badge merah muncul hanya jika ada notifikasi belum dibaca --}}
                        @if($unreadCount > 0)
                        <span class="absolute top-1 right-1 inline-flex items-center justify-center min-w-[16px] h-4 px-1 text-[9px] font-bold text-white bg-red-500 rounded-full ring-2 ring-white">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                        @endif
                    </button>

                    {{-- Dropdown notifikasi (maks 5 terbaru) --}}
                    <div x-show="open" x-transition @click.outside="open = false"
                         class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                            <span class="font-bold text-gray-800 text-sm">Pesanan Masuk</span>
                            <a href="{{ route('admin.orders.index') }}" class="text-xs text-blue-600 hover:underline font-semibold">Lihat Semua</a>
                        </div>
                        <div class="divide-y divide-gray-50 max-h-64 overflow-y-auto">
                            @forelse(auth()->user()->notifications()->latest()->take(5)->get() as $notif)
                                <a href="{{ route('admin.orders.index') }}"
                                   class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition {{ $notif->read_at ? 'opacity-60' : '' }}">
                                    <span class="text-xl mt-0.5">🛒</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-800 truncate">
                                            {{ $notif->data['customer_name'] }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $notif->data['order_code'] }} &bull;
                                            Rp {{ number_format($notif->data['total'], 0, ',', '.') }}
                                        </p>
                                        <p class="text-[10px] text-gray-400 mt-0.5">
                                            {{ $notif->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    @if(!$notif->read_at)
                                        <span class="w-2 h-2 bg-blue-500 rounded-full mt-1.5 flex-shrink-0"></span>
                                    @endif
                                </a>
                            @empty
                                <p class="text-center text-sm text-gray-400 py-6">Belum ada notifikasi.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-2 cursor-pointer p-1 rounded-full hover:bg-gray-50">
                    <img src="https://placehold.co/150x150/3b82f6/ffffff?text=AD" class="h-8 w-8 rounded-full object-cover" alt="Avatar">
                    <span class="text-sm font-medium text-gray-700">Admin</span>
                </div>
            </div>
        </header>

        {{-- Script: mark all notifications as read saat dropdown dibuka --}}
        <script>
        function markRead() {
            fetch("{{ url('/admin/notifications/read-all') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
            });
        }
        </script>

        <main class="flex-1 overflow-y-auto p-4 md:p-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl card-shadow border border-slate-100 flex flex-col justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Total Penjualan</p>
                        <h3 class="text-3xl font-bold text-gray-900">
                            Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="mt-4">
                        @if($revenuePercentage > 0)
                            <p class="text-xs flex items-center text-green-600 bg-green-50 w-fit px-2 py-1 rounded-full font-medium">
                                <i data-lucide="arrow-up-right" class="w-3 h-3 mr-1"></i>
                                +{{ number_format(abs($revenuePercentage), 1) }}% minggu ini
                            </p>
                        @elseif($revenuePercentage < 0)
                            <p class="text-xs flex items-center text-red-600 bg-red-50 w-fit px-2 py-1 rounded-full font-medium">
                                <i data-lucide="arrow-down-right" class="w-3 h-3 mr-1"></i>
                                -{{ number_format(abs($revenuePercentage), 1) }}% minggu ini
                            </p>
                        @else
                            <p class="text-xs flex items-center text-gray-500 bg-gray-100 w-fit px-2 py-1 rounded-full font-medium">
                                <i data-lucide="minus" class="w-3 h-3 mr-1"></i>
                                Stabil minggu ini
                            </p>
                        @endif
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl card-shadow border border-slate-100 flex flex-col justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Pesanan Hari Ini</p>
                        <h3 class="text-3xl font-bold text-gray-900">{{ $ordersToday }}</h3>
                    </div>
                    <div class="mt-4">
                        @if($orderPercentage > 0)
                            <p class="text-xs flex items-center text-green-600 bg-green-50 w-fit px-2 py-1 rounded-full font-medium">
                                <i data-lucide="arrow-up-right" class="w-3 h-3 mr-1"></i>
                                +{{ number_format(abs($orderPercentage), 0) }}% dari kemarin
                            </p>
                        @elseif($orderPercentage < 0)
                            <p class="text-xs flex items-center text-red-600 bg-red-50 w-fit px-2 py-1 rounded-full font-medium">
                                <i data-lucide="arrow-down-right" class="w-3 h-3 mr-1"></i>
                                -{{ number_format(abs($orderPercentage), 0) }}% dari kemarin
                            </p>
                        @else
                            <p class="text-xs flex items-center text-gray-500 bg-gray-100 w-fit px-2 py-1 rounded-full font-medium">
                                <i data-lucide="minus" class="w-3 h-3 mr-1"></i>
                                Sama dengan kemarin
                            </p>
                        @endif
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl card-shadow border border-slate-100 flex flex-col justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Pengguna Terdaftar</p>
                        <h3 class="text-3xl font-bold text-gray-900">{{ $totalUsers }}</h3>
                    </div>
                    <div class="mt-4">
                        @if($userPercentage > 0)
                            <p class="text-xs flex items-center text-blue-600 bg-blue-50 w-fit px-2 py-1 rounded-full font-medium">
                                <i data-lucide="users" class="w-3 h-3 mr-1"></i>
                                +{{ number_format(abs($userPercentage), 1) }}% user baru minggu ini
                            </p>
                        @elseif($userPercentage < 0)
                            <p class="text-xs flex items-center text-red-600 bg-red-50 w-fit px-2 py-1 rounded-full font-medium">
                                <i data-lucide="users" class="w-3 h-3 mr-1"></i>
                                Penurunan user minggu ini
                            </p>
                        @else
                            <p class="text-xs flex items-center text-gray-500 bg-gray-100 w-fit px-2 py-1 rounded-full font-medium">
                                <i data-lucide="minus" class="w-3 h-3 mr-1"></i>
                                Tidak ada user baru minggu ini
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- WIDGET: Pesanan Sudah Dibayar, Belum Diproses --}}
            {{-- Menampilkan hingga 5 pesanan dengan payment_status='paid' dan fulfillment_status='pending'. --}}
            @if($pendingPaidOrders->isNotEmpty())
            <div class="bg-white rounded-2xl card-shadow border border-orange-100 mb-8 overflow-hidden">
                <div class="px-6 py-4 bg-orange-50 border-b border-orange-100 flex items-center justify-between">
                    <h3 class="font-bold text-orange-800 flex items-center gap-2">
                        🔔 Pesanan Perlu Diproses
                        <span class="bg-orange-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingPaidOrders->count() }}</span>
                    </h3>
                    <a href="{{ route('admin.orders.index') }}" class="text-sm text-orange-600 hover:underline font-semibold">Kelola Semua →</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($pendingPaidOrders as $po)
                    <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition">
                        <div>
                            <p class="font-bold text-gray-800">{{ $po->customer_name }}
                                <span class="text-xs font-normal text-gray-400 ml-2">#{{ $po->code }}</span>
                            </p>
                            <p class="text-sm text-gray-500">{{ $po->city }} &bull; {{ $po->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-blue-600">Rp {{ number_format($po->total, 0, ',', '.') }}</p>
                            <a href="{{ route('admin.orders.index') }}" class="text-xs text-orange-600 font-semibold hover:underline">Proses →</a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="bg-white p-6 rounded-2xl card-shadow border border-slate-100 mb-8">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Tren Penjualan 7 Hari Terakhir</h3>
                <canvas id="salesChart"></canvas>
            </div>

            <footer class="text-center text-sm text-gray-500 pt-4 border-t border-slate-100">
                &copy; 2025 KIAT Dashboard Admin. Semua hak dilindungi.
            </footer>
        </main>
    </div>

    <script>
        lucide.createIcons();

        // ============================================
        // SCRIPT CHART.JS DENGAN DATA REAL DARI LARAVEL
        // ============================================
        const ctx = document.getElementById('salesChart');

        new Chart(ctx, {
            type: 'line',
            data: {
                // MENGAMBIL DATA LABEL DARI CONTROLLER
                labels: {!! json_encode($chartLabels) !!}, 
                datasets: [{
                    label: 'Penjualan (Rp)',
                    // MENGAMBIL DATA NOMINAL DARI CONTROLLER
                    data: {!! json_encode($chartData) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.1)',
                    fill: true,
                    tension: 0.3, // Sedikit melengkung agar terlihat bagus
                    pointRadius: 4,
                    pointBackgroundColor: '#3b82f6'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false // Sembunyikan legenda jika hanya 1 dataset
                    },
                    tooltip: {
                        callbacks: {
                            // Format Rupiah di Tooltip saat hover
                            label: function(context) {
                                let value = context.raw;
                                return ' Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            // Format Rupiah di Sumbu Y
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID', { notation: "compact" }).format(value);
                            }
                        }
                    }
                }
            }
        });
    </script>

</body>
</html>
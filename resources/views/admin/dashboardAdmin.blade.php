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
                <i data-lucide="snowflake" class="w-6 h-6 mr-2 text-blue-500"></i>
                <span class="text-blue-600">KIAT</span><span class="text-gray-900">Dashboard</span>
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
                <div class="flex items-center space-x-2 cursor-pointer p-1 rounded-full hover:bg-gray-50">
                    <img src="https://placehold.co/150x150/3b82f6/ffffff?text=AD" class="h-8 w-8 rounded-full object-cover" alt="Avatar">
                    <span class="text-sm font-medium text-gray-700">Admin</span>
                </div>
            </div>
        </header>

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
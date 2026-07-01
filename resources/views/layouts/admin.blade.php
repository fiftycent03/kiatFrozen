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
    
    <!-- Alpine.js dengan Persist Plugin untuk mengingat status sidebar -->
    <script src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; overflow-x: hidden; }
        .card-shadow { box-shadow: 0 10px 15px -3px rgba(59,130,246,0.1), 0 4px 6px -2px rgba(59,130,246,0.05); }
        [x-cloak] { display: none !important; }
        
        .sidebar-transition {
            transition: transform 0.3s ease-in-out, margin-left 0.3s ease-in-out;
        }
    </style>
</head>

<!-- LOGIKA: Menggunakan $persist agar status sidebarOpen tersimpan di browser -->
<body class="min-h-screen flex" x-data="{ sidebarOpen: $persist(true) }">

    <!-- SIDEBAR -->
    <aside id="sidebar"
           class="sidebar-transition fixed inset-y-0 left-0 bg-white w-64 p-4 flex flex-col border-r border-slate-100 z-30"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           x-cloak>

        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center">
                <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                    <!-- Logo resmi perusahaan (menggantikan ikon kepingan salju + teks "KIAT") -->
                    <img src="{{ asset('storage/Logo_Kiat.png') }}" alt="Logo KIAT" class="h-10 w-auto mr-2">
                    <span class="font-bold text-xl text-blue-800 tracking-tight">Karya Inti Alam Tunggal</span>
                </h1>
            </div>
            <!-- Tombol Close hanya untuk Mobile -->
            <button @click="sidebarOpen = false" class="md:hidden text-gray-500 p-1 hover:bg-gray-100 rounded-lg">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <nav class="flex-grow space-y-1" x-data="{ openDashboard: $persist(true) }">
            <!-- Menu Dashboard -->
            <div class="mb-1">
                <div class="flex items-center justify-between w-full p-2 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition {{ request()->routeIs('admin.dashboard*') ? 'bg-blue-50 text-blue-600' : '' }}">
                    <a href="{{ route('admin.dashboard') }}" class="flex-grow flex items-center p-1">
                        <i data-lucide="menu" class="w-5 h-5 mr-3"></i>
                        <span>Dashboard</span>
                    </a>
                    <button @click="openDashboard = !openDashboard" class="p-1 focus:outline-none">
                        <i :class="openDashboard ? 'rotate-180' : 'rotate-0'" data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200"></i>
                    </button>
                </div>
                <div x-show="openDashboard" x-transition class="mt-1 ml-8 flex flex-col space-y-1 border-l-2 border-slate-100 pl-2">
                    <a href="{{ route('admin.orders.index') }}" class="text-gray-600 text-sm p-2 rounded-lg hover:text-blue-600 transition">Pesanan Baru</a>
                    <a href="{{ route('admin.sales.index') }}" class="text-gray-600 text-sm p-2 rounded-lg hover:text-blue-600 transition">Total Penjualan</a>
                </div>
            </div>

            <!-- Menu Produk -->
            <a href="{{ route('admin.products.index') }}" 
               class="flex items-center p-3 rounded-xl {{ request()->routeIs('admin.products*') ? 'bg-blue-50 text-blue-600' : 'text-gray-500' }} hover:bg-gray-50 hover:text-gray-800 font-medium transition">
                <i data-lucide="package" class="w-5 h-5 mr-3"></i>Produk
            </a>

            <!-- Menu Pengguna -->
            <a href="{{ route('admin.users.index') }}"
               class="flex items-center p-3 rounded-xl {{ request()->routeIs('admin.users*') ? 'bg-blue-50 text-blue-600' : 'text-gray-500' }} hover:bg-gray-50 hover:text-gray-800 font-medium transition">
                <i data-lucide="users" class="w-5 h-5 mr-3"></i>Pengguna
            </a>

            <!-- Menu Kurir -->
            <a href="{{ route('admin.couriers.index') }}"
               class="flex items-center p-3 rounded-xl {{ request()->routeIs('admin.couriers*') ? 'bg-blue-50 text-blue-600' : 'text-gray-500' }} hover:bg-gray-50 hover:text-gray-800 font-medium transition">
                <i data-lucide="truck" class="w-5 h-5 mr-3"></i>Kurir
            </a>

            <div class="mt-auto pt-10">
                <form action="{{ route('logout') }}" method="GET">
                    <button type="submit" class="flex items-center w-full p-3 rounded-xl text-red-500 hover:bg-red-50 font-medium transition">
                        <i data-lucide="log-out" class="w-5 h-5 mr-3"></i>Keluar
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    <!-- KONTEN UTAMA -->
    <div class="flex-1 flex flex-col overflow-hidden sidebar-transition"
         :class="sidebarOpen ? 'md:ml-64' : 'ml-0'">
        
        <header class="flex items-center justify-between p-4 bg-white border-b border-slate-100 sticky top-0 z-20">
            <div class="flex items-center">
                <!-- HAMBURGER BUTTON UTAMA: Untuk buka & tutup -->
                <button @click="sidebarOpen = !sidebarOpen" 
                        class="p-2 mr-4 text-gray-600 bg-gray-50 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all shadow-sm">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                <h2 class="text-xl font-semibold text-gray-800">Admin Area</h2>
            </div>

            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2 cursor-pointer p-1 rounded-full hover:bg-gray-50">
                    <img src="https://placehold.co/150x150/3b82f6/ffffff?text=AD" class="h-8 w-8 rounded-full object-cover" alt="Avatar">
                    <span class="text-sm font-medium text-gray-700 hidden sm:inline">Admin</span>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-8">
            @yield('content')
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
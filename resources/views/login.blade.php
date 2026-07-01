    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> Login - KIAT</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
            body {
                font-family: 'Inter', sans-serif;
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
            }
            .icy-bg {
                background: linear-gradient(135deg, #E0F7FA 0%, #B3E0F2 50%, #90D3F7 100%);
            }
            .icy-card {
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                background-color: rgba(255, 255, 255, 0.75);
                border: 1px solid rgba(255, 255, 255, 0.5);
            }
            @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
            .animate-spin-slow { animation: spin 30s linear infinite; }
        </style>
    </head>
    <body class="icy-bg">

        <!-- Card utama -->
        <div class="icy-card p-8 sm:p-12 w-11/12 max-w-md mx-auto rounded-3xl shadow-2xl shadow-cyan-500/40 transform transition duration-500 hover:scale-[1.01] overflow-hidden">

            <!-- Logo resmi perusahaan (menggantikan ikon lingkaran berputar + gembok) -->
            <div class="flex flex-col items-center mb-6">
                <img src="{{ asset('storage/Logo_Kiat.png') }}" alt="Logo KIAT" class="h-16 w-auto mb-2">
                <span class="font-bold text-lg text-blue-800 tracking-tight text-center">Karya Inti Alam Tunggal</span>
            </div>

            <h1 class="text-4xl font-extrabold text-center text-gray-800 mb-2">Login</h1>
            <p class="text-center text-gray-500 mb-8">Silahkan Melakukan Login</p>

            {{-- Pesan Error atau Sukses --}}
            @if(session('error'))
                <div class="bg-red-100 text-red-700 p-3 mb-4 rounded-xl text-sm text-center">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 mb-4 rounded-xl text-sm text-center">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Form login --}}
            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <div class="relative">
                        <input type="email" id="email" name="email" required placeholder="masukkan email"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition duration-300 hover:border-cyan-400 bg-white/80 text-gray-700 shadow-inner">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-1 10a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h12a2 2 0 012 2v10z"></path>
                        </svg>
                    </div>
                </div>

                <div class="mb-8">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Kata Sandi</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required placeholder="masukkan password"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition duration-300 hover:border-cyan-400 bg-white/80 text-gray-700 shadow-inner">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                </div>

                <div class="text-center mt-6">
    <p class="text-sm text-gray-600">
        Belum punya akun?
        <a href="{{ route('register') }}" class="text-cyan-600 hover:underline font-semibold">
            Daftar sekarang
        </a>
    </p>
</div>

                <div>
                    <button type="submit"
                            class="w-full py-3 bg-cyan-600 text-white font-semibold rounded-xl shadow-lg shadow-cyan-500/50 hover:bg-cyan-500 active:scale-[0.98] transition duration-200 transform">
                        Masuk
                    </button>
                </div>
            </form>

        </div>
    </body>
    </html>

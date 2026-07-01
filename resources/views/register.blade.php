<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - KIAT</title>
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

<div class="icy-card p-8 sm:p-12 w-11/12 max-w-md mx-auto rounded-3xl shadow-2xl shadow-cyan-500/40">

    <!-- Logo resmi perusahaan (menggantikan ikon lingkaran berputar + kunci) -->
    <div class="flex flex-col items-center mb-6">
        <img src="{{ asset('storage/Logo_Kiat.png') }}" alt="Logo KIAT" class="h-16 w-auto mb-2">
        <span class="font-bold text-lg text-blue-800 tracking-tight text-center">Karya Inti Alam Tunggal</span>
    </div>

    <h1 class="text-4xl font-extrabold text-center text-gray-800 mb-2">Daftar Akun</h1>
    <p class="text-center text-gray-500 mb-6">Buat akun baru Anda di KIAT</p>

    {{-- Error message --}}
    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 mb-4 rounded-xl text-sm">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Success message --}}
    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-3 mb-4 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('register.post') }}">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium">Nama Lengkap</label>
            <input type="text" name="name" required
                class="w-full mt-1 p-3 rounded-xl border border-gray-300"/>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Email</label>
            <input type="email" name="email" required
                class="w-full mt-1 p-3 rounded-xl border border-gray-300"/>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Kata Sandi</label>
            <input type="password" name="password" required
                class="w-full mt-1 p-3 rounded-xl border border-gray-300"/>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Konfirmasi Kata Sandi</label>
            <input type="password" name="password_confirmation" required
                class="w-full mt-1 p-3 rounded-xl border border-gray-300"/>
        </div>

        <button type="submit"
            class="w-full py-3 bg-cyan-600 text-white rounded-xl hover:bg-cyan-500">
            Daftar
        </button>
    </form>

    <div class="text-center mt-6">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="text-cyan-600 font-semibold hover:underline">
            Masuk sekarang
        </a>
    </div>

</div>

</body>
</html>

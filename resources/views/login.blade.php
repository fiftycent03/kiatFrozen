<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — KIAT</title>

    {{-- Konten disesuaikan dengan Tema Abyss/Pearl/Gold (halaman auth standalone, --}}
    {{-- konfigurasi Tailwind & font disertakan inline karena tidak memakai layout). --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;0,9..144,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                colors: { abyss:'#071726', marine:'#0B3E56', lagoon:'#16808A', pearl:'#F6F1E7', ink:'#101B22', gold:'#D4AF37', coral:'#E2683F' },
                fontFamily: { display:['"Fraunces"','serif'], sans:['"Inter"','ui-sans-serif','sans-serif'], mono:['"JetBrains Mono"','ui-monospace','monospace'] },
                boxShadow: { glow:'0 10px 30px -8px rgba(212,175,55,0.35)' },
            } },
        };
    </script>
    <style>
        body { -webkit-font-smoothing: antialiased; }
        :focus-visible { outline: 2px solid #D4AF37; outline-offset: 3px; border-radius: 4px; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-b from-abyss to-marine font-sans text-ink flex items-center justify-center p-5">

    {{-- Glow dekoratif lagoon/gold sesuai tema hero --}}
    <div class="pointer-events-none fixed left-1/2 top-0 h-[520px] w-[520px] -translate-x-1/2 rounded-full bg-lagoon/20 blur-[130px]"></div>
    <div class="pointer-events-none fixed bottom-0 right-0 h-72 w-72 rounded-full bg-gold/10 blur-3xl"></div>

    {{-- Card utama (pearl glass) --}}
    <div class="relative w-11/12 max-w-md rounded-3xl border border-white/50 bg-pearl/95 p-8 shadow-2xl shadow-black/40 backdrop-blur sm:p-12">

        {{-- Logo resmi perusahaan --}}
        <div class="mb-6 flex flex-col items-center">
            <img src="{{ asset('storage/Logo_Kiat.png') }}" alt="Logo KIAT" class="mb-3 h-16 w-16 rounded-full object-cover border border-ink/10 shadow-md">
            <span class="font-mono text-[10px] uppercase tracking-[0.18em] text-lagoon">Karya Inti Alam Tunggal</span>
        </div>

        <h1 class="text-center font-display text-4xl font-semibold text-ink">Masuk</h1>
        <p class="mb-8 mt-1 text-center text-ink/50">Silakan masuk ke akun KIAT Anda</p>

        {{-- Pesan Error atau Sukses --}}
        @if(session('error'))
            <div class="mb-4 rounded-xl border border-coral/20 bg-coral/10 p-3 text-center text-sm text-coral">
                {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div class="mb-4 rounded-xl border border-lagoon/20 bg-lagoon/10 p-3 text-center text-sm text-lagoon">
                {{ session('success') }}
            </div>
        @endif

        {{-- Form login --}}
        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="mb-6">
                <label for="email" class="mb-2 block text-sm font-medium text-ink/80">Email</label>
                <div class="relative">
                    <input type="email" id="email" name="email" required placeholder="masukkan email"
                        class="w-full rounded-xl border border-ink/15 bg-white/80 py-3 pl-10 pr-4 text-ink shadow-inner transition duration-300 hover:border-lagoon/50 focus:border-lagoon focus:ring-2 focus:ring-lagoon/40">
                    <svg class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-ink/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-1 10a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h12a2 2 0 012 2v10z"></path>
                    </svg>
                </div>
            </div>

            <div class="mb-8">
                <label for="password" class="mb-2 block text-sm font-medium text-ink/80">Kata Sandi</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required placeholder="masukkan password"
                        class="w-full rounded-xl border border-ink/15 bg-white/80 py-3 pl-10 pr-4 text-ink shadow-inner transition duration-300 hover:border-lagoon/50 focus:border-lagoon focus:ring-2 focus:ring-lagoon/40">
                    <svg class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-ink/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
            </div>

            <button type="submit"
                    class="w-full rounded-xl bg-gold py-3.5 font-semibold text-abyss shadow-glow transition-transform duration-200 hover:scale-[1.02] active:scale-[0.98]">
                Masuk
            </button>

            <p class="mt-6 text-center text-sm text-ink/60">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-semibold text-lagoon transition hover:text-gold">Daftar sekarang</a>
            </p>
        </form>
    </div>
</body>
</html>

@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto">

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Kurir</h2>
        <p class="text-gray-500 text-sm">Tambah dan kelola akun kurir yang bertugas mengantar pesanan.</p>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl flex items-center gap-2">
        <i data-lucide="check-circle" class="w-5 h-5"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-2xl flex items-center gap-2">
        <i data-lucide="alert-circle" class="w-5 h-5"></i>
        <span class="font-medium">{{ session('error') }}</span>
    </div>
    @endif

    {{-- Form Tambah Kurir --}}
    <div class="bg-white rounded-2xl card-shadow border border-slate-100 p-6 mb-6">
        <h3 class="font-bold text-gray-700 text-lg mb-4 flex items-center gap-2">
            <i data-lucide="user-plus" class="w-5 h-5 text-blue-500"></i>
            Tambah Akun Kurir Baru
        </h3>
        <form action="{{ route('admin.couriers.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="Cth: Budi Santoso"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 @error('name') border-red-400 @enderror">
                    @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           placeholder="kurir@kiatfrozen.com"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 @error('email') border-red-400 @enderror">
                    @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="max-w-sm">
                <label class="block text-sm font-semibold text-gray-600 mb-1">Password</label>
                <input type="password" name="password" required
                       placeholder="Minimal 6 karakter"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 @error('password') border-red-400 @enderror">
                @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition active:scale-95 shadow-lg shadow-blue-200">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Buat Akun Kurir
                </button>
            </div>
        </form>
    </div>

    {{-- Daftar Kurir --}}
    <div class="bg-white rounded-2xl card-shadow border border-slate-100 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center gap-2">
            <i data-lucide="truck" class="w-5 h-5 text-orange-500"></i>
            <h3 class="font-bold text-gray-700">Daftar Kurir Aktif</h3>
            <span class="ml-auto bg-orange-100 text-orange-700 text-xs font-bold px-2 py-0.5 rounded-full">
                {{ $couriers->count() }} kurir
            </span>
        </div>

        @if($couriers->isEmpty())
        <div class="p-12 text-center text-gray-400">
            <i data-lucide="truck" class="w-10 h-10 mx-auto mb-3 opacity-30"></i>
            <p class="text-sm">Belum ada kurir terdaftar. Tambah kurir pertama di atas.</p>
        </div>
        @else
        <ul class="divide-y divide-slate-100">
            @foreach($couriers as $courier)
            <li class="flex items-center justify-between px-5 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-orange-100 flex items-center justify-center">
                        <i data-lucide="user" class="w-4 h-4 text-orange-600"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-800 text-sm">{{ $courier->name }}</div>
                        <div class="text-xs text-gray-400">{{ $courier->email }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-[10px] text-gray-400">Dibuat {{ $courier->created_at->diffForHumans() }}</span>
                    <form action="{{ route('admin.couriers.destroy', $courier->id) }}" method="POST"
                          onsubmit="return confirm('Hapus akun kurir {{ $courier->name }}? Pesanan yang sudah ditugaskan tidak akan terhapus.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="text-red-400 hover:text-red-600 p-1.5 rounded-lg hover:bg-red-50 transition">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </li>
            @endforeach
        </ul>
        @endif
    </div>

</div>

<script>
    lucide.createIcons();
</script>
@endsection

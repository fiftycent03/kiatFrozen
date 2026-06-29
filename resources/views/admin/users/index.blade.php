@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Daftar Pengguna</h2>
            <p class="text-sm text-gray-500">List semua user yang telah melakukan registrasi.</p>
        </div>
        
        <div class="flex items-center gap-3 w-full md:w-auto">
            {{-- FORM SEARCH --}}
            <form action="{{ route('admin.users.index') }}" method="GET" class="relative flex-grow md:w-64">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari nama atau email..." 
                       class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm transition shadow-sm">
                <div class="absolute left-3 top-2.5 text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                {{-- Tombol Reset jika sedang mencari --}}
                @if(request('search'))
                    <a href="{{ route('admin.users.index') }}" class="absolute right-3 top-2 text-xs bg-slate-100 hover:bg-slate-200 p-1 rounded text-slate-500">
                        Reset
                    </a>
                @endif
            </form>

            <div class="bg-blue-50 text-blue-600 px-4 py-2 rounded-lg font-medium text-sm flex items-center shrink-0">
                <i data-lucide="users" class="w-4 h-4 mr-2"></i>
                Total: {{ $users->total() }} Akun
            </div>
        </div>
    </div>

    {{-- Alert Info jika sedang mencari --}}
    @if(request('search'))
        <div class="bg-blue-50 border border-blue-100 p-3 rounded-xl text-sm text-blue-700">
            Menampilkan hasil pencarian untuk: <strong>"{{ request('search') }}"</strong>
        </div>
    @endif

    <div class="bg-white rounded-2xl card-shadow border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-gray-600 text-xs uppercase tracking-wider border-b border-slate-100">
                        <th class="p-4 font-semibold">Nama Pengguna</th>
                        <th class="p-4 font-semibold">Email</th>
                        <th class="p-4 font-semibold">Role</th>
                        <th class="p-4 font-semibold">Bergabung Pada</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-gray-700">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50 transition">
                        
                        <td class="p-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold mr-3">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-400">ID: #{{ $user->id }}</div>
                                </div>
                            </div>
                        </td>

                        <td class="p-4">
                            <span class="text-gray-600">{{ $user->email }}</span>
                        </td>

                        <td class="p-4">
                            @if($user->role === 'admin')
                                <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded text-xs font-bold border border-purple-200">
                                    ADMIN
                                </span>
                            @else
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-medium border border-gray-200">
                                    User
                                </span>
                            @endif
                        </td>

                        <td class="p-4">
                            <div class="flex items-center text-gray-500">
                                <i data-lucide="calendar" class="w-3 h-3 mr-1.5"></i>
                                {{ $user->created_at->format('d M Y') }}
                            </div>
                            <div class="text-xs text-gray-400 mt-0.5 ml-4">
                                {{ $user->created_at->format('H:i') }} WIB
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-500">
                            @if(request('search'))
                                Tidak ada pengguna yang cocok dengan kata kunci "{{ request('search') }}".
                            @else
                                Belum ada pengguna yang terdaftar.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{-- Penting: appends(request()->query()) agar search tidak hilang saat pindah page pagination --}}
            {{ $users->appends(request()->query())->links() }}
        </div>
    </div>

</div>

<script>
    lucide.createIcons();
</script>
@endsection 
@extends('layouts.admin')

@section('content')

    {{-- Style Tambahan Khusus Halaman Ini --}}
    <style>
        .icy-shadow {
            box-shadow: 0 10px 30px -10px rgba(30, 144, 255, .2);
        }
    </style>

    {{-- Flash message --}}
    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 flex items-center">
            <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="flex flex-col xl:flex-row gap-6">

        <aside class="w-full xl:w-[260px] shrink-0">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 sticky top-6">
                <h3 class="font-bold text-lg mb-4 flex items-center text-gray-700">
                    <i data-lucide="filter" class="w-5 h-5 mr-2 text-blue-500"></i>
                    Filter Produk
                </h3>

                <form method="GET" action="{{ route('admin.products.index') }}" class="space-y-4">

    <div>
        <label class="block text-sm font-semibold mb-1 text-gray-600">Cari Produk</label>
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Ketik minimal 2 huruf..."
            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-blue-500 transition">
    </div>

    <div>
        <label class="block text-sm font-semibold mb-1 text-gray-600">Kategori</label>
        
        {{-- Ubah name menjadi category_id agar sesuai dengan struktur database --}}
        <select name="category_id"
            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-blue-500 transition">
            
            <option value="">Semua Kategori</option>

            {{-- Ambil data langsung dari Model Category, sama seperti halaman Create --}}
            @foreach(\App\Models\Category::all() as $cat)
                <option value="{{ $cat->id }}" 
                    {{-- Cek apakah ID di URL sama dengan ID kategori ini --}}
                    {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
            
        </select>
    </div>

    <div class="space-y-2 pt-2">
        <button
            class="w-full bg-blue-600 text-white py-2 rounded-xl hover:bg-blue-700 font-semibold text-sm transition shadow-lg shadow-blue-100">
            Terapkan Filter
        </button>

        <a href="{{ route('admin.products.index') }}"
            class="block w-full text-center bg-gray-100 text-gray-600 py-2 rounded-xl hover:bg-gray-200 text-sm transition">
            Reset
        </a>
    </div>
</form>

                @if (request('search') && strlen(request('search')) < 2)
                    <div class="mt-4 text-xs text-red-500">
                        * Minimal 2 huruf untuk pencarian.
                    </div>
                @endif
            </div>
        </aside>

        <div class="flex-1 min-w-0">

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 icy-shadow">

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pb-4 border-b border-gray-100 gap-4">
                    <h1 class="text-2xl font-extrabold flex items-center text-gray-800">
                        <i data-lucide="snowflake" class="w-6 h-6 mr-3 text-blue-500 fill-blue-100"></i>
                        Manajemen Produk
                    </h1>

                    <a href="{{ route('admin.products.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-semibold flex items-center transition shadow-lg shadow-blue-200">
                        <i data-lucide="plus-circle" class="w-4 h-4 mr-2"></i> Tambah Produk
                    </a>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-gray-100">
                    <table class="min-w-full divide-y divide-gray-100">

                        <thead class="bg-slate-50 text-slate-600 uppercase text-xs tracking-wider font-bold">
                            <tr>
                                <th class="p-4 text-left">#</th>
                                <th class="p-4 text-left">Foto</th>
                                <th class="p-4 text-left">Nama</th>
                                <th class="p-4 text-left">Harga</th>
                                <th class="p-4 text-left">Min</th>
                                <th class="p-4 text-left">Status</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-100 text-sm">
                            @forelse ($products as $product)
                                <tr class="hover:bg-blue-50/50 transition duration-150">

                                    <td class="p-4 text-gray-500">{{ $products->firstItem() + $loop->index }}</td>

                                    <td class="p-4">
                                        @if ($product->primaryImage)
                                            <img src="{{ asset('storage/' . $product->primaryImage->path) }}"
                                                class="w-12 h-12 rounded-lg object-cover border border-gray-200 shadow-sm">
                                        @else
                                            <div
                                                class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center text-xs text-gray-400 font-medium">
                                                No IMG</div>
                                        @endif
                                    </td>

                                    <td class="p-4 font-bold text-gray-700">{{ $product->name }}</td>

                                    <td class="p-4 font-mono font-medium text-green-600">
                                        Rp{{ number_format($product->price_per_kg, 0, ',', '.') }}
                                    </td>

                                    <td class="p-4 text-gray-600">
                                        {{ $product->min_pembelian }} {{ $product->satuan }}
                                    </td>

                                    <td class="p-4">
                                        @if ($product->is_active)
                                            <span
                                                class="bg-green-100 text-green-700 px-3 py-1 text-xs font-bold rounded-full border border-green-200">Aktif</span>
                                        @else
                                            <span
                                                class="bg-gray-100 text-gray-600 px-3 py-1 text-xs font-bold rounded-full border border-gray-200">Nonaktif</span>
                                        @endif
                                    </td>

                                    <td class="p-4 text-center">
                                        <div class="flex justify-center gap-2">

                                            <a href="{{ route('admin.products.edit', $product) }}"
                                                class="bg-yellow-50 hover:bg-yellow-100 text-yellow-600 border border-yellow-200 px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center transition">
                                                <i data-lucide="pencil" class="w-3 h-3 mr-1"></i>Edit
                                            </a>

                                            <button type="button"
                                                onclick="showDeleteModal('delete-form-{{ $product->id }}')"
                                                class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center transition">
                                                <i data-lucide="trash-2" class="w-3 h-3 mr-1"></i>Hapus
                                            </button>

                                            <form id="delete-form-{{ $product->id }}"
                                                action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                                class="hidden">
                                                @csrf @method('DELETE')
                                            </form>

                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <i data-lucide="package-open" class="w-12 h-12 mb-3 text-gray-300"></i>
                                            <p class="text-lg font-medium text-gray-500">Belum ada produk</p>
                                            <p class="text-sm">Silakan tambah produk baru untuk memulai.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $products->withQueryString()->links() }}
                </div>

            </div>
        </div>
    </div>

    <div id="confirmation-modal"
        class="fixed inset-0 bg-gray-900/60 z-50 hidden items-center justify-center backdrop-blur-sm transition-all duration-300">
        <div class="bg-white p-6 rounded-2xl shadow-2xl max-w-sm w-full transform transition-all scale-95" id="modal-content">
            <div class="flex items-center justify-center w-12 h-12 bg-red-100 rounded-full mx-auto mb-4">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
            </div>
            <h3 class="text-xl font-bold text-center text-gray-800 mb-2">Konfirmasi Hapus</h3>
            <p class="text-gray-500 text-center mb-6 text-sm">Apakah Anda yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex space-x-3">
                <button id="modal-cancel"
                    class="flex-1 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-medium transition">
                    Batal
                </button>
                <button id="modal-confirm"
                    class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 font-medium shadow-lg shadow-red-200 transition">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    <script>
        // Modal Logic
        const modal = document.getElementById('confirmation-modal');
        const modalContent = document.getElementById('modal-content');
        const modalConfirm = document.getElementById('modal-confirm');
        const modalCancel = document.getElementById('modal-cancel');
        let pendingId = null;

        function showDeleteModal(formId) {
            pendingId = formId;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            // Animasi kecil saat muncul
            setTimeout(() => {
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
        }

        function hideModal() {
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 150);
        }

        modalCancel.addEventListener('click', hideModal);

        modalConfirm.addEventListener('click', () => {
            if (pendingId) document.getElementById(pendingId).submit();
        });

        // Close on click outside
        modal.addEventListener('click', (e) => {
            if (e.target === modal) hideModal();
        });
    </script>

@endsection
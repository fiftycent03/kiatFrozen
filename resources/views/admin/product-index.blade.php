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

                    {{-- Tombol "Tambah Kategori" diletakkan bersisian dengan "Tambah Produk".
                         Sebelumnya fitur ini ada di form Tambah Produk, sekarang dipindah ke sini
                         supaya kategori bisa dikelola tanpa harus masuk ke form tambah produk. --}}
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="openAddCategoryModal()"
                            class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2.5 rounded-xl font-semibold flex items-center transition shadow-lg shadow-teal-200">
                            <i data-lucide="tag" class="w-4 h-4 mr-2"></i> Tambah Kategori
                        </button>

                        <a href="{{ route('admin.products.create') }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-semibold flex items-center transition shadow-lg shadow-blue-200">
                            <i data-lucide="plus-circle" class="w-4 h-4 mr-2"></i> Tambah Produk
                        </a>
                    </div>
                </div>

                {{-- ============================================================= --}}
                {{-- DAFTAR KATEGORI: tabel kecil di atas tabel produk, tiap baris --}}
                {{-- punya tombol Edit yang membuka modal edit kategori. --}}
                {{-- ============================================================= --}}
                <div class="mb-6 border border-gray-100 rounded-2xl overflow-hidden">
                    <div class="bg-slate-50 px-4 py-3 flex items-center justify-between">
                        <h2 class="font-bold text-gray-700 text-sm flex items-center">
                            <i data-lucide="tags" class="w-4 h-4 mr-2 text-teal-600"></i>
                            Daftar Kategori
                        </h2>
                        <span class="bg-teal-100 text-teal-700 text-xs font-bold px-2 py-0.5 rounded-full">
                            {{ $categories->count() }} kategori
                        </span>
                    </div>

                    @if($categories->isEmpty())
                    <div class="p-6 text-center text-gray-400 text-sm">Belum ada kategori. Tambahkan lewat tombol di atas.</div>
                    @else
                    <div class="overflow-x-auto">
                        <div class="flex gap-3 p-4 min-w-max">
                            @foreach($categories as $cat)
                            <div class="w-44 shrink-0 border border-gray-100 rounded-xl p-3 flex flex-col items-center text-center">
                                {{-- Preview gambar kategori dari storage (fallback placeholder bila kosong) --}}
                                <img src="{{ $cat->image ? asset('storage/' . $cat->image) : 'https://placehold.co/100x100?text=No+Img' }}"
                                     class="w-16 h-16 rounded-lg object-cover border border-gray-200 mb-2" alt="{{ $cat->name }}">
                                <p class="font-semibold text-gray-700 text-sm truncate w-full">{{ $cat->name }}</p>
                                @if($cat->is_active)
                                    <span class="mt-1 bg-green-100 text-green-700 px-2 py-0.5 text-[10px] font-bold rounded-full">Aktif</span>
                                @else
                                    <span class="mt-1 bg-gray-100 text-gray-500 px-2 py-0.5 text-[10px] font-bold rounded-full">Nonaktif</span>
                                @endif

                                {{-- Tombol Edit: kirim data kategori ini ke JS agar modal edit terisi otomatis --}}
                                <button type="button"
                                    onclick="openEditCategoryModal({{ $cat->id }}, {{ Js::from($cat->name) }}, {{ Js::from($cat->image) }}, {{ $cat->is_active ? 'true' : 'false' }})"
                                    class="mt-2 w-full bg-yellow-50 hover:bg-yellow-100 text-yellow-600 border border-yellow-200 px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center justify-center transition">
                                    <i data-lucide="pencil" class="w-3 h-3 mr-1"></i>Edit
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
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

    {{-- ============================================================= --}}
    {{-- MODAL TAMBAH KATEGORI (nama + upload gambar) --}}
    {{-- ============================================================= --}}
    <div id="modal-add-category" class="fixed inset-0 bg-gray-900/60 z-50 hidden items-center justify-center backdrop-blur-sm p-4">
        <div class="bg-white p-6 rounded-2xl shadow-2xl max-w-md w-full">
            <h3 class="text-lg font-bold mb-4 flex items-center">
                <i data-lucide="tag" class="w-5 h-5 mr-2 text-teal-600"></i>Tambah Kategori Baru
            </h3>
            {{-- enctype wajib agar file gambar ikut terkirim --}}
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label class="block text-sm font-semibold mb-2">Nama Kategori</label>
                <input type="text" name="name" required class="w-full border rounded-xl px-4 py-3 mb-4" placeholder="Contoh: Cumi-Cumi">

                <label class="block text-sm font-semibold mb-2">Gambar Kategori (opsional)</label>
                <input type="file" name="image" accept="image/*" class="w-full mb-4">

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeAddCategoryModal()" class="bg-gray-100 hover:bg-gray-200 px-5 py-2.5 rounded-xl font-medium">Batal</button>
                    <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2.5 rounded-xl font-bold">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================================= --}}
    {{-- MODAL EDIT KATEGORI (preview gambar saat ini + ganti nama/gambar) --}}
    {{-- ============================================================= --}}
    <div id="modal-edit-category" class="fixed inset-0 bg-gray-900/60 z-50 hidden items-center justify-center backdrop-blur-sm p-4">
        <div class="bg-white p-6 rounded-2xl shadow-2xl max-w-md w-full">
            <h3 class="text-lg font-bold mb-4 flex items-center">
                <i data-lucide="pencil" class="w-5 h-5 mr-2 text-yellow-600"></i>Edit Kategori
            </h3>

            {{-- Preview gambar kategori SAAT INI, sebelum diganti --}}
            <div class="flex justify-center mb-4">
                <img id="edit-cat-preview" src="" alt="" class="w-24 h-24 rounded-xl object-cover border border-gray-200">
            </div>

            {{-- Action form di-set dinamis via JS (openEditCategoryModal) ke admin.categories.update/{id} --}}
            <form id="form-edit-category" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <label class="block text-sm font-semibold mb-2">Nama Kategori</label>
                <input type="text" name="name" id="edit-cat-name" required class="w-full border rounded-xl px-4 py-3 mb-4">

                <label class="block text-sm font-semibold mb-2">Ganti Gambar (opsional)</label>
                <input type="file" name="image" accept="image/*" class="w-full mb-4">

                <label class="flex items-center gap-2 text-sm font-semibold mb-4">
                    <input type="checkbox" name="is_active" id="edit-cat-active" value="1"> Kategori Aktif
                </label>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeEditCategoryModal()" class="bg-gray-100 hover:bg-gray-200 px-5 py-2.5 rounded-xl font-medium">Batal</button>
                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2.5 rounded-xl font-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Base URL storage — dipakai untuk membangun path gambar kategori di modal edit.
        const storageBase = "{{ asset('storage') }}";

        function openAddCategoryModal() {
            document.getElementById('modal-add-category').classList.replace('hidden', 'flex');
        }
        function closeAddCategoryModal() {
            document.getElementById('modal-add-category').classList.replace('flex', 'hidden');
        }

        // Dipanggil dari tombol "Edit" tiap kartu kategori. Mengisi form edit
        // dengan data kategori yang dipilih (termasuk preview gambar saat ini)
        // dan mengarahkan action form ke endpoint update kategori tsb.
        function openEditCategoryModal(id, name, imagePath, isActive) {
            const form = document.getElementById('form-edit-category');
            // Route admin.categories.update/{category} — dibangun manual karena id dinamis.
            form.action = "{{ url('/admin/categories') }}/" + id;

            document.getElementById('edit-cat-name').value = name;
            document.getElementById('edit-cat-active').checked = !!isActive;

            // Preview gambar saat ini; pakai placeholder bila kategori belum punya gambar.
            const preview = document.getElementById('edit-cat-preview');
            preview.src = imagePath ? (storageBase + '/' + imagePath) : 'https://placehold.co/100x100?text=No+Img';

            document.getElementById('modal-edit-category').classList.replace('hidden', 'flex');
        }
        function closeEditCategoryModal() {
            document.getElementById('modal-edit-category').classList.replace('flex', 'hidden');
        }
    </script>

@endsection
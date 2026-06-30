<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - KIAT FROZEN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .toggle-checkbox:checked+.toggle-label .toggle-circle {
            transform: translateX(1.5rem);
        }

        .toggle-checkbox:checked+.toggle-label {
            background-color: #3b82f6;
        }
    </style>
</head>

<body class="bg-indigo-50 min-h-screen py-10 px-4 sm:px-8 font-sans">

    <header class="max-w-4xl mx-auto mb-6">
        <h1 class="text-3xl font-extrabold text-gray-800">
            <span class="text-blue-600">KIAT</span>Dashboard
        </h1>
    </header>

    <div class="max-w-4xl mx-auto bg-white p-8 rounded-3xl shadow-2xl shadow-indigo-200 border border-gray-100">
        <h2 class="text-2xl font-bold border-b pb-4 mb-6">
            Formulir Produk Baru
        </h2>

        {{-- ERROR HANDLER --}}
        @if ($errors->any())
        <div class="bg-red-100 p-4 mb-6 rounded">
            <ul class="text-red-600">
                @foreach ($errors->all() as $error)
                <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold mb-2">Nama Produk</label>
                    <input type="text" name="name" required value="{{ old('name') }}"
                        class="w-full border rounded-xl px-4 py-3">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">SKU Root</label>
                    <input type="text" name="sku_root" required value="{{ old('sku_root') }}"
                        class="w-full border rounded-xl px-4 py-3">
                </div>
            </div>

            <div class="mb-6">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-semibold">Kategori</label>
                    {{-- Tombol pemicu modal tambah kategori INLINE (tanpa pindah halaman) --}}
                    <button type="button" id="btn-add-category" class="text-xs font-bold text-blue-600 hover:underline">
                        + Tambah Kategori Baru
                    </button>
                </div>

                <select name="category_id" id="category-select" required class="w-full border rounded-xl px-4 py-3">

                    <option value="">-- Pilih Kategori --</option>

                    @foreach(\App\Models\Category::where('is_active',1)->get() as $cat)
                    <option value="{{ $cat->id }}">
                        {{ $cat->name }}
                    </option>
                    @endforeach

                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold mb-2">Harga per Kg</label>
                    <input type="number" name="price_per_kg" required min="0" value="{{ old('price_per_kg') }}"
                        class="w-full border rounded-xl px-4 py-3">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2 text-blue-600">Stok Awal</label>
                    <input type="number" name="stock" required min="0" value="{{ old('stock') ?? 0 }}"
                        class="w-full border border-blue-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                <div>
                    <label class="block text-sm font-semibold mb-2">Minimal Pembelian</label>
                    <input type="number" name="min_pembelian" required min="1" value="{{ old('min_pembelian') ?? 1 }}"
                        class="w-full border rounded-xl px-4 py-3">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Satuan</label>
                    <select name="satuan" required class="w-full border rounded-xl px-4 py-3">
                        <option value="">-- Pilih Satuan --</option>
                        <option value="kg">Kg</option>
                        <option value="pcs">Pcs</option>
                    </select>
                </div>

            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold mb-2">Deskripsi</label>
                <textarea name="description" rows="4"
                    class="w-full border rounded-xl px-4 py-3">{{ old('description') }}</textarea>
            </div>

            <div class="mb-8 flex items-center justify-between p-4 bg-gray-50 rounded-xl border">
                <div>
                    <label class="text-sm font-semibold">Status Produk</label>
                    <span id="status-text" class="text-xs text-gray-500">Nonaktif</span>
                </div>

                <div id="toggle-wrapper" class="relative w-12 h-6 rounded-full bg-gray-300 cursor-pointer">

                    <div id="toggle-circle" class="absolute w-5 h-5 bg-white rounded-full top-0.5 left-0.5"></div>

                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" class="hidden">
                </div>
            </div>

            <div class="mb-8">
                <label class="block font-semibold mb-2">Foto Produk</label>
                <input type="file" name="images[]" multiple accept="image/*" class="w-full">
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.products.index') }}" class="bg-gray-200 px-6 py-3 rounded-xl">Batal</a>
                <button class="bg-blue-600 text-white px-6 py-3 rounded-xl">Simpan Produk</button>
            </div>

        </form>
    </div>


    <script>
        const toggleWrapper = document.getElementById("toggle-wrapper");
        const circle = document.getElementById("toggle-circle");
        const checkbox = document.getElementById("is_active");
        const status = document.getElementById("status-text");

        function update() {
            if (checkbox.checked) {
                toggleWrapper.classList.add("bg-blue-500");
                toggleWrapper.classList.remove("bg-gray-300");
                circle.style.transform = "translateX(24px)";
                status.textContent = "Aktif";
            } else {
                toggleWrapper.classList.add("bg-gray-300");
                toggleWrapper.classList.remove("bg-blue-500");
                circle.style.transform = "translateX(0)";
                status.textContent = "Nonaktif";
            }
        }

        toggleWrapper.onclick = () => {
            checkbox.checked = !checkbox.checked;
            update();
        };

        update();
    </script>

    {{-- ============================================================= --}}
    {{-- MODAL TAMBAH KATEGORI INLINE (via AJAX) --}}
    {{-- ============================================================= --}}
    <div id="category-modal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl">
            <h3 class="text-lg font-bold mb-4">Tambah Kategori Baru</h3>

            <label class="block text-sm font-semibold mb-2">Nama Kategori</label>
            <input type="text" id="new-cat-name" class="w-full border rounded-xl px-4 py-3 mb-4" placeholder="Contoh: Kerang">

            <label class="block text-sm font-semibold mb-2">Gambar Kategori (opsional)</label>
            <input type="file" id="new-cat-image" accept="image/*" class="w-full mb-2">

            <p id="cat-modal-error" class="text-red-500 text-sm mb-2 hidden"></p>

            <div class="flex justify-end gap-3 mt-4">
                <button type="button" id="btn-cancel-category" class="bg-gray-200 px-5 py-2 rounded-xl">Batal</button>
                <button type="button" id="btn-save-category" class="bg-blue-600 text-white px-5 py-2 rounded-xl font-bold">Simpan</button>
            </div>
        </div>
    </div>

    <script>
        // Referensi elemen modal & dropdown kategori.
        const catModal     = document.getElementById('category-modal');
        const catSelect    = document.getElementById('category-select');
        const catNameInput = document.getElementById('new-cat-name');
        const catImgInput  = document.getElementById('new-cat-image');
        const catError     = document.getElementById('cat-modal-error');

        // Buka / tutup modal.
        document.getElementById('btn-add-category').onclick   = () => catModal.classList.replace('hidden', 'flex');
        document.getElementById('btn-cancel-category').onclick = () => catModal.classList.replace('flex', 'hidden');

        // SIMPAN kategori via AJAX -> tidak perlu reload halaman.
        document.getElementById('btn-save-category').onclick = function () {
            catError.classList.add('hidden');
            const name = catNameInput.value.trim();
            if (!name) { catError.textContent = 'Nama kategori wajib diisi.'; catError.classList.remove('hidden'); return; }

            // Bungkus data (nama + gambar) ke FormData agar file ikut terkirim.
            const formData = new FormData();
            formData.append('name', name);
            if (catImgInput.files[0]) formData.append('image', catImgInput.files[0]);
            // Ambil CSRF token dari hidden input @csrf milik form produk.
            formData.append('_token', document.querySelector('input[name=_token]').value);

            // Kirim ke endpoint admin.categories.store (mengembalikan JSON).
            fetch("{{ route('admin.categories.store') }}", {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) throw new Error('Gagal menyimpan');
                // Tambahkan kategori baru ke dropdown lalu pilih otomatis.
                const opt = new Option(data.name, data.id, true, true);
                catSelect.add(opt);
                // Reset & tutup modal.
                catNameInput.value = ''; catImgInput.value = '';
                catModal.classList.replace('flex', 'hidden');
            })
            .catch(() => { catError.textContent = 'Gagal menyimpan kategori. Coba lagi.'; catError.classList.remove('hidden'); });
        };
    </script>

</body>

</html>
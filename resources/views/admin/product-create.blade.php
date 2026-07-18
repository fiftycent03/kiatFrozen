<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - KIAT FROZEN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Alpine.js dipakai untuk reaktivitas form: toggle Pcs/Kg & repeater baris Varian --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .toggle-checkbox:checked+.toggle-label .toggle-circle {
            transform: translateX(1.5rem);
        }

        .toggle-checkbox:checked+.toggle-label {
            background-color: #3b82f6;
        }

        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-indigo-50 min-h-screen py-10 px-4 sm:px-8 font-sans">

    <header class="max-w-4xl mx-auto mb-6 flex items-center">
        <!-- Logo resmi perusahaan (menggantikan teks "KIAT Dashboard") -->
        <img src="{{ asset('storage/Logo_Kiat.png') }}" alt="Logo Karya Inti Alam Tunggal"
            class="h-11 w-11 rounded-full object-cover shadow-md border border-white/20" />
        <span class="font-bold text-xl text-blue-800 tracking-tight">Karya Inti Alam Tunggal</span>
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

        {{-- x-data root: `satuan` = saklar Pcs/Kg (juga jadi value dropdown "Tipe Penjualan"),
             `variants` = array baris Varian Potongan/Gramasi yang bisa ditambah/dihapus. --}}
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data"
              {{-- Field stok dihapus atas permintaan --}}
              x-data="{
                  satuan: '{{ old('satuan', 'pcs') }}',
                  variants: {{ old('variants') ? Js::from(old('variants')) : '[{label:\'\',price:\'\'}]' }},
                  addVariant() { this.variants.push({ label: '', price: '' }); },
                  removeVariant(i) { if (this.variants.length > 1) this.variants.splice(i, 1); },
              }">
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
                    {{-- Tombol & modal tambah kategori dipindahkan ke halaman Manajemen Produk
                         (product-index.blade.php), di sebelah tombol "Tambah Produk". --}}
                </div>

                <select name="category_id" id="category-select" required class="w-full border rounded-xl px-4 py-3">

                    <option value="">-- Pilih Kategori --</option>

                    @foreach(\App\Models\Category::where('is_active', 1)->get() as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                    @endforeach

                </select>
            </div>

            {{-- ============================================================= --}}
            {{-- TIPE PENJUALAN / SATUAN — SATU dropdown, DUA fungsi:           --}}
            {{-- (1) mengisi kolom lama `satuan` (teks tampilan di halaman lain) --}}
            {{-- (2) via x-model="satuan" mengendalikan logika If/Else Pcs/Kg   --}}
            {{-- di bawah (harga tunggal vs form varian dinamis).               --}}
            {{-- ============================================================= --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold mb-2">Tipe Penjualan (Satuan)</label>
                <select name="satuan" x-model="satuan" required class="w-full border rounded-xl px-4 py-3">
                    <option value="pcs">Pcs — harga & stok tunggal</option>
                    <option value="kg">Kg — wajib isi varian potongan/gramasi</option>
                </select>
            </div>

            {{-- ============================================================= --}}
            {{-- CABANG "PCS": harga & stok UTAMA. Input di-nonaktifkan          --}}
            {{-- (:disabled) saat satuan=kg agar TIDAK ikut terkirim ke server —  --}}
            {{-- x-show hanya menyembunyikan tampilan, browser tetap mengirim    --}}
            {{-- field yang tidak disabled walau disembunyikan CSS.              --}}
            {{-- ============================================================= --}}
            {{-- Field stok dihapus atas permintaan --}}
            <div x-show="satuan === 'pcs'" x-cloak>
                <div class="mb-6">
                    <label class="block text-sm font-semibold mb-2">Harga per Pcs</label>
                    <input type="number" name="price_per_kg" :disabled="satuan === 'kg'" min="0"
                        value="{{ old('price_per_kg') }}"
                        class="w-full border rounded-xl px-4 py-3">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold mb-2">Minimal Pembelian</label>
                    <input type="number" name="min_pembelian" :disabled="satuan === 'kg'" min="1"
                        value="{{ old('min_pembelian') ?? 1 }}"
                        class="w-full border rounded-xl px-4 py-3">
                </div>
            </div>

            {{-- ============================================================= --}}
            {{-- CABANG "KG": form dinamis Varian Potongan/Gramasi (Alpine       --}}
            {{-- repeater). Tiap baris = satu potongan dengan harga & stok       --}}
            {{-- sendiri, mis. "500 gram" Rp25.000 / stok 10.                   --}}
            {{-- ============================================================= --}}
            <div x-show="satuan === 'kg'" x-cloak class="mb-6">
                <label class="block text-sm font-semibold mb-3">Varian Potongan / Gramasi</label>

                <div class="space-y-3">
                    {{-- Field stok dihapus atas permintaan --}}
                    <template x-for="(variant, index) in variants" :key="index">
                        <div class="grid grid-cols-1 sm:grid-cols-[2fr_1.2fr_auto] gap-3 items-center bg-gray-50 border border-gray-200 rounded-xl p-3">
                            <input type="text" placeholder="Nama Potongan (mis. 500 gram)"
                                x-model="variant.label" :name="'variants[' + index + '][label]'"
                                :disabled="satuan === 'pcs'"
                                class="border rounded-lg px-3 py-2 text-sm">
                            <input type="number" placeholder="Harga" min="0"
                                x-model="variant.price" :name="'variants[' + index + '][price]'"
                                :disabled="satuan === 'pcs'"
                                class="border rounded-lg px-3 py-2 text-sm">
                            <button type="button" @click="removeVariant(index)"
                                class="text-red-500 hover:text-red-700 text-sm font-semibold px-2 py-2">
                                Hapus
                            </button>
                        </div>
                    </template>
                </div>

                <button type="button" @click="addVariant()"
                    class="mt-3 text-sm font-semibold text-blue-600 hover:text-blue-800">
                    + Tambah Varian
                </button>
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

            {{-- ============================================================= --}}
            {{-- MULTI-FOTO PRODUK — maksimal 5 foto. Validasi jumlah dicek     --}}
            {{-- di sisi klien (peringatan instan) DAN di server (final).       --}}
            {{-- ============================================================= --}}
            <div class="mb-8">
                <label class="block font-semibold mb-2">Foto Produk (maksimal 5)</label>
                <input type="file" name="images[]" id="images-input" multiple accept="image/*" class="w-full">
                <p id="images-counter" class="mt-1.5 text-xs text-gray-400">0 dari 5 foto dipilih.</p>
                <p id="images-warning" class="mt-1 text-xs text-red-500 hidden">Maksimal 5 foto — kelebihan file diabaikan.</p>
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

        // ---------------------------------------------------------------
        // VALIDASI JUMLAH FOTO (klien): batasi maksimal 5 file terpilih.
        // Backend (ProductController@store) tetap jadi validasi FINAL —
        // ini hanya feedback instan agar Admin tidak perlu submit dulu.
        // ---------------------------------------------------------------
        const imagesInput = document.getElementById('images-input');
        const imagesCounter = document.getElementById('images-counter');
        const imagesWarning = document.getElementById('images-warning');

        imagesInput.addEventListener('change', function () {
            const count = this.files.length;
            imagesCounter.textContent = count + ' dari 5 foto dipilih.';
            imagesWarning.classList.toggle('hidden', count <= 5);
        });
    </script>

</body>

</html>
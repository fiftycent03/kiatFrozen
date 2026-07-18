<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - KIAT Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    {{-- Alpine.js: toggle Pcs/Kg + repeater baris Varian (sama seperti form Tambah) --}}
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

    <header class="max-w-4xl mx-auto mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-extrabold text-gray-800">
            <span class="text-blue-600">Edit</span> Produk
        </h1>
    </header>

    <div class="max-w-4xl mx-auto bg-white p-8 rounded-3xl shadow-2xl shadow-indigo-200 border border-gray-100">

        {{-- Error validation --}}
        @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700">
            <p class="font-semibold mb-2">Ada kesalahan input:</p>
            <ul class="list-disc ml-5 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- x-data di-seed dari data produk yang sudah ada di database:
             `satuan` dari $product->satuan, `variants` dari $product->variants (jika kosong,
             mulai dengan 1 baris kosong supaya Admin langsung bisa mengisi saat ganti ke Kg). --}}
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data"
              {{-- Field stok dihapus atas permintaan --}}
              x-data="{
                  satuan: '{{ old('satuan', $product->satuan) }}',
                  variants: {{ old('variants')
                        ? Js::from(old('variants'))
                        : ($product->variants->isNotEmpty()
                            ? Js::from($product->variants->map(fn($v) => ['label' => $v->label, 'price' => $v->price])->values())
                            : '[{label:\'\',price:\'\'}]') }},
                  addVariant() { this.variants.push({ label: '', price: '' }); },
                  removeVariant(i) { if (this.variants.length > 1) this.variants.splice(i, 1); },
              }">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold mb-2 text-gray-700">Nama Produk</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2 text-gray-700">SKU Root</label>
                    <input type="text" name="sku_root" value="{{ old('sku_root', $product->sku_root) }}" required
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold mb-2 text-gray-700">Kategori</label>
                <select name="category_id" required
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">-- Pilih Kategori --</option>
                    {{-- Loop Kategori dari Database --}}
                    @foreach(\App\Models\Category::where('is_active',1)->get() as $cat)
                        <option value="{{ $cat->id }}"
                            {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- ============================================================= --}}
            {{-- TIPE PENJUALAN / SATUAN — dropdown tunggal, mengendalikan       --}}
            {{-- logika Pcs/Kg via x-model="satuan" (lihat komentar sama di      --}}
            {{-- product-create.blade.php).                                     --}}
            {{-- ============================================================= --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold mb-2 text-gray-700">Tipe Penjualan (Satuan)</label>
                <select name="satuan" x-model="satuan" required
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <option value="pcs">Pcs — harga & stok tunggal</option>
                    <option value="kg">Kg — wajib isi varian potongan/gramasi</option>
                </select>
            </div>

            {{-- CABANG "PCS": harga utama (disabled saat Kg agar tidak ikut terkirim). Field stok dihapus atas permintaan. --}}
            <div x-show="satuan === 'pcs'" x-cloak>
                <div class="mb-6">
                    <label class="block text-sm font-semibold mb-2 text-gray-700">Harga per Kg (Rp)</label>
                    <input type="number" name="price_per_kg" :disabled="satuan === 'kg'" min="0"
                        value="{{ old('price_per_kg', $product->price_per_kg) }}"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold mb-2 text-gray-700">Minimal Pembelian</label>
                    <input type="number" name="min_pembelian" :disabled="satuan === 'kg'" min="1"
                        value="{{ old('min_pembelian', $product->min_pembelian) }}"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>
            </div>

            {{-- CABANG "KG": repeater Varian, seeded dari $product->variants (lihat x-data di atas). --}}
            <div x-show="satuan === 'kg'" x-cloak class="mb-6">
                <label class="block text-sm font-semibold mb-3 text-gray-700">Varian Potongan / Gramasi</label>

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
                <p class="mt-2 text-xs text-amber-600">Menyimpan akan MENGGANTI seluruh varian lama dengan daftar di atas.</p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold mb-2 text-gray-700">Deskripsi Produk</label>
                <textarea name="description" rows="4"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">{{ old('description', $product->description) }}</textarea>
            </div>

            {{-- ============================================================= --}}
            {{-- GALERI FOTO — menampilkan SEMUA foto produk (bukan cuma satu   --}}
            {{-- seperti versi lama). Tiap foto punya: radio "Jadikan Utama"    --}}
            {{-- + checkbox "Hapus". Ditambah input upload multi-foto baru      --}}
            {{-- (total foto lama tersisa + foto baru maksimal 5 — divalidasi   --}}
            {{-- ProductController@update).                                    --}}
            {{-- ============================================================= --}}
            <div class="mb-8">
                <label class="block text-sm font-semibold mb-3 text-gray-700">Galeri Foto (maksimal 5 total)</label>

                @if($product->images->isEmpty())
                    <p class="text-sm text-gray-400 italic mb-4">Belum ada foto untuk produk ini.</p>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-4">
                        @foreach($product->images as $img)
                            <div class="relative border border-gray-200 rounded-xl p-3 text-center">
                                <img src="{{ asset('storage/' . $img->path) }}"
                                     class="w-full h-28 object-cover rounded-lg border border-gray-100 mb-2">

                                <label class="flex items-center justify-center gap-1.5 text-xs font-semibold text-gray-600 mb-1 cursor-pointer">
                                    <input type="radio" name="primary_image_id" value="{{ $img->id }}"
                                        {{ $img->is_primary ? 'checked' : '' }}>
                                    Foto Utama
                                </label>

                                <label class="flex items-center justify-center gap-1.5 text-xs font-semibold text-red-500 cursor-pointer">
                                    <input type="checkbox" name="delete_images[]" value="{{ $img->id }}">
                                    Hapus foto ini
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endif

                <label class="flex flex-col w-full h-32 border-2 border-dashed border-gray-300 rounded-xl hover:bg-blue-50 hover:border-blue-400 transition cursor-pointer justify-center items-center group">
                    <div class="flex flex-col items-center justify-center pt-4 pb-5">
                        <i data-lucide="image-plus" class="w-7 h-7 text-gray-400 mb-1.5 group-hover:text-blue-500 transition"></i>
                        <p class="text-sm text-gray-500 font-medium group-hover:text-blue-600">Klik untuk tambah foto baru</p>
                        <p id="images-counter" class="text-xs text-gray-400 mt-1">0 foto baru dipilih.</p>
                    </div>
                    <input type="file" name="images[]" id="images-input" multiple accept="image/*" class="hidden">
                </label>
                <p id="images-warning" class="mt-1.5 text-xs text-red-500 hidden">
                    Total foto (lama yang tersisa + baru) akan melebihi 5 — kelebihannya akan ditolak server.
                </p>
            </div>

            @php
                $isActive = old('is_active', $product->is_active);
            @endphp

            <div class="mb-8 flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
                <div>
                    <label class="text-sm font-semibold text-gray-700">Status Produk</label>
                    <div id="status-text" class="text-xs text-gray-500 mt-1">
                        {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                    </div>
                </div>

                <div id="toggle-wrapper" class="relative w-12 h-6 rounded-full cursor-pointer transition-colors duration-300 {{ $isActive ? 'bg-blue-500' : 'bg-gray-300' }}">
                    <div id="toggle-circle"
                         class="absolute w-5 h-5 bg-white rounded-full top-0.5 left-0.5 transition-transform duration-300 shadow-sm"
                         style="transform: {{ $isActive ? 'translateX(24px)' : 'translateX(0)' }}"></div>

                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" class="hidden" {{ $isActive ? 'checked' : '' }}>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.products.index') }}"
                    class="bg-gray-100 text-gray-600 px-6 py-3 rounded-xl hover:bg-gray-200 font-medium transition">
                    Batal
                </a>

                <button type="submit"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-xl font-medium shadow-lg shadow-yellow-200 transition">
                    Simpan Perubahan
                </button>
            </div>

        </form>

    </div>
    <script>
        // Init Icons
        lucide.createIcons();

        // 1. LOGIC TOGGLE STATUS
        const toggleWrapper = document.getElementById('toggle-wrapper');
        const toggleCircle  = document.getElementById('toggle-circle');
        const checkbox      = document.getElementById('is_active');
        const statusText    = document.getElementById('status-text');

        function updateToggleUI() {
            if (checkbox.checked) {
                toggleWrapper.classList.add('bg-blue-500');
                toggleWrapper.classList.remove('bg-gray-300');
                toggleCircle.style.transform = 'translateX(24px)';
                statusText.textContent = 'Aktif (Tampil)';
                statusText.classList.add('text-blue-600');
            } else {
                toggleWrapper.classList.add('bg-gray-300');
                toggleWrapper.classList.remove('bg-blue-500');
                toggleCircle.style.transform = 'translateX(0)';
                statusText.textContent = 'Nonaktif (Sembunyi)';
                statusText.classList.remove('text-blue-600');
            }
        }

        toggleWrapper.addEventListener('click', () => {
            checkbox.checked = !checkbox.checked;
            updateToggleUI();
        });

        updateToggleUI();

        // ---------------------------------------------------------------
        // VALIDASI JUMLAH FOTO (klien): total foto lama yang TIDAK dicentang
        // hapus + foto baru yang dipilih, dibandingkan ke batas 5. Backend
        // (ProductController@update) tetap validasi FINAL.
        // ---------------------------------------------------------------
        const imagesInput = document.getElementById('images-input');
        const imagesCounter = document.getElementById('images-counter');
        const imagesWarning = document.getElementById('images-warning');
        const existingTotal = {{ $product->images->count() }};

        function countCheckedDeletes() {
            return document.querySelectorAll('input[name="delete_images[]"]:checked').length;
        }

        function refreshImageCount() {
            const newCount = imagesInput.files.length;
            imagesCounter.textContent = newCount + ' foto baru dipilih.';
            const remainingOld = existingTotal - countCheckedDeletes();
            imagesWarning.classList.toggle('hidden', (remainingOld + newCount) <= 5);
        }

        imagesInput.addEventListener('change', refreshImageCount);
        document.querySelectorAll('input[name="delete_images[]"]').forEach((el) => {
            el.addEventListener('change', refreshImageCount);
        });
    </script>

</body>

</html>
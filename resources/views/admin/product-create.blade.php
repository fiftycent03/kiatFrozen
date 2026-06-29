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
                <label class="block text-sm font-semibold mb-2">Kategori</label>

                <select name="category_id" required class="w-full border rounded-xl px-4 py-3">

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

</body>

</html>
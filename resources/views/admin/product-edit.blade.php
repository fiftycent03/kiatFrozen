<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - KIAT Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script> 
    
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

        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold mb-2 text-gray-700">Harga per Kg (Rp)</label>
                    <input type="number" name="price_per_kg" min="0"
                        value="{{ old('price_per_kg', $product->price_per_kg) }}" required
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2 text-blue-600 font-bold">Stok Saat Ini</label>
                    <input type="number" name="stock" min="0"
                        value="{{ old('stock', $product->stock) }}" required
                        class="w-full border border-blue-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition bg-blue-50">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold mb-2 text-gray-700">Minimal Pembelian</label>
                    <input type="number" name="min_pembelian" min="1"
                        value="{{ old('min_pembelian', $product->min_pembelian) }}" required
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2 text-gray-700">Satuan</label>
                    <select name="satuan" required
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <option value="">-- Pilih Satuan --</option>
                        <option value="kg" {{ old('satuan', $product->satuan) == 'kg' ? 'selected' : '' }}>Kg</option>
                        <option value="pcs" {{ old('satuan', $product->satuan) == 'pcs' ? 'selected' : '' }}>Pcs</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold mb-2 text-gray-700">Deskripsi Produk</label>
                <textarea name="description" rows="4"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="mb-8">
                <label class="block text-sm font-semibold mb-2 text-gray-700">Foto Produk</label>
                
                <div class="flex flex-col md:flex-row gap-6 items-start">
                    <div class="shrink-0 relative group">
                        @php
                            $currentImage = $product->images->first() 
                                            ? asset('storage/' . $product->images->first()->path) 
                                            : 'https://placehold.co/400?text=No+Image';
                        @endphp
                        <img id="image-preview" 
                             src="{{ $currentImage }}" 
                             class="w-40 h-40 object-cover rounded-xl border border-gray-200 shadow-sm bg-gray-50">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition rounded-xl"></div>
                    </div>

                    <div class="w-full">
                        <label class="flex flex-col w-full h-40 border-2 border-dashed border-gray-300 rounded-xl hover:bg-blue-50 hover:border-blue-400 transition cursor-pointer justify-center items-center group">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i data-lucide="image-plus" class="w-8 h-8 text-gray-400 mb-2 group-hover:text-blue-500 transition"></i>
                                <p class="text-sm text-gray-500 font-medium group-hover:text-blue-600">Klik untuk ganti foto baru</p>
                                <p class="text-xs text-gray-400 mt-1">Foto lama akan otomatis terganti</p>
                            </div>
                            <input type="file" name="images[]" id="image-input" accept="image/*" class="hidden">
                        </label>
                    </div>
                </div>
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

        // 1. LOGIC IMAGE PREVIEW
        const imageInput = document.getElementById('image-input');
        const imagePreview = document.getElementById('image-preview');

        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

        // 2. LOGIC TOGGLE STATUS
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
    </script>

</body>

</html>
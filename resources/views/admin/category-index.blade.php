<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori - KIAT FROZEN</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-indigo-50 min-h-screen py-10 px-4 sm:px-8 font-sans">

    <header class="max-w-5xl mx-auto mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-extrabold text-gray-800">
            <span class="text-blue-600">Kelola</span> Kategori
        </h1>
        <a href="{{ route('admin.products.index') }}" class="text-sm font-bold text-gray-600 hover:text-blue-600">&larr; Kembali ke Produk</a>
    </header>

    @if(session('success'))
    <div class="max-w-5xl mx-auto mb-4 bg-green-100 text-green-700 px-4 py-3 rounded-xl">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
    <div class="max-w-5xl mx-auto mb-4 bg-red-100 text-red-600 px-4 py-3 rounded-xl">
        @foreach ($errors->all() as $error) <div>• {{ $error }}</div> @endforeach
    </div>
    @endif

    <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- FORM TAMBAH KATEGORI (dengan upload gambar) --}}
        <div class="bg-white p-6 rounded-3xl shadow-xl border border-gray-100 h-fit">
            <h2 class="text-lg font-bold mb-4">➕ Tambah Kategori</h2>
            {{-- enctype wajib agar file gambar ikut terkirim --}}
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label class="block text-sm font-semibold mb-2">Nama Kategori</label>
                <input type="text" name="name" required class="w-full border rounded-xl px-4 py-3 mb-4" placeholder="Contoh: Cumi-Cumi">

                <label class="block text-sm font-semibold mb-2">Gambar Kategori</label>
                <input type="file" name="image" accept="image/*" class="w-full mb-4">

                <button class="w-full bg-blue-600 text-white px-6 py-3 rounded-xl font-bold">Simpan Kategori</button>
            </form>
        </div>

        {{-- DAFTAR KATEGORI + EDIT GAMBAR --}}
        <div class="md:col-span-2 space-y-4">
            @forelse($categories as $cat)
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex gap-4 items-center">
                {{-- Preview gambar kategori dari storage (fallback placeholder bila kosong) --}}
                <img src="{{ $cat->image ? asset('storage/' . $cat->image) : 'https://placehold.co/100x100?text=No+Img' }}"
                     class="w-20 h-20 rounded-xl object-cover border" alt="{{ $cat->name }}">

                <form action="{{ route('admin.categories.update', $cat->id) }}" method="POST" enctype="multipart/form-data" class="flex-1">
                    @csrf @method('PUT')
                    <input type="text" name="name" value="{{ $cat->name }}" required class="w-full border rounded-lg px-3 py-2 mb-2 font-bold">
                    <div class="flex items-center gap-3">
                        {{-- Upload untuk MENGGANTI gambar kategori (opsional) --}}
                        <input type="file" name="image" accept="image/*" class="text-sm flex-1">
                        <label class="text-xs flex items-center gap-1"><input type="checkbox" name="is_active" value="1" {{ $cat->is_active ? 'checked' : '' }}> Aktif</label>
                        <button class="bg-yellow-500 text-white px-4 py-2 rounded-lg text-sm font-bold">Update</button>
                    </div>
                </form>

                <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                    @csrf @method('DELETE')
                    <button class="text-red-500 text-sm font-bold">Hapus</button>
                </form>
            </div>
            @empty
            <div class="bg-white p-8 rounded-2xl text-center text-gray-500">Belum ada kategori.</div>
            @endforelse
        </div>
    </div>

</body>

</html>

@extends('layouts.app')

@section('content')
{{-- Konten disesuaikan dengan Tema Abyss/Pearl/Gold: variabel warna lama di-remap --}}
{{-- ke palet baru (icy-blue → lagoon, ice-bg → pearl, deep-ocean → ink). --}}
<style>
    :root {
        --icy-blue: #16808A;                    /* lagoon — aksen teal */
        --frost-white: #ffffff;
        --deep-ocean: #101B22;                  /* ink — teks utama */
        --ice-bg: #F6F1E7;                       /* pearl — dasar lembut */
        --crystal-border: rgba(16,27,34,0.10);   /* border ink lembut */
    }
    body { background-color: var(--ice-bg); font-family: 'Inter', sans-serif; }
    /* CTA utama & badge memakai aksen emas (gold) sesuai tema premium */
    .btn-kiat { background: #D4AF37 !important; color: #071726 !important; }
    .badge-default { background: #D4AF37 !important; color: #071726 !important; }
    .page-title { font-weight: 800; color: var(--deep-ocean); margin-bottom: 30px; }
    .grid-container { display: grid; grid-template-columns: 1fr 400px; gap: 30px; max-width: 1200px; margin: auto; padding: 20px; }
    
    /* Card Styling */
    .kiat-card { background: white; border-radius: 24px; padding: 25px; border: 1px solid var(--crystal-border); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); }
    .address-item { border: 2px solid var(--ice-bg); border-radius: 20px; padding: 20px; margin-bottom: 15px; position: relative; transition: 0.3s; }
    .address-item.active { border-color: var(--icy-blue); background: var(--ice-bg); }
    
    .badge-default { background: var(--icy-blue); color: white; padding: 4px 10px; border-radius: 8px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; margin-bottom: 10px; display: inline-block; }
    
    .kiat-input { width: 100%; padding: 14px; border-radius: 15px; border: 1.5px solid var(--crystal-border); background: var(--ice-bg); margin-bottom: 15px; font-weight: 600; box-sizing: border-box; outline: none; }
    .btn-kiat { width: 100%; padding: 16px; border-radius: 18px; border: none; background: var(--icy-blue); color: white; font-weight: 800; cursor: pointer; transition: 0.3s; }
    .btn-kiat:hover { filter: brightness(1.1); transform: translateY(-2px); }

    @media (max-width: 1024px) { .grid-container { grid-template-columns: 1fr; } }
</style>

<div class="grid-container">
    {{-- DAFTAR ALAMAT --}}
    <div>
        <h2 class="page-title">📖 Buku Alamat</h2>
        @forelse($addresses as $addr)
            <div class="address-item {{ $addr->is_default ? 'active' : '' }}">
                @if($addr->is_default)
                    <span class="badge-default">Utama</span>
                @endif
                <div style="font-weight: 800; font-size: 1.1rem;">{{ $addr->label }} — {{ $addr->customer_name }}</div>
                <div style="font-weight: 600; color: var(--icy-blue); margin-top: 5px;">{{ $addr->customer_phone }}</div>
                <p style="color: #64748b; font-size: 0.9rem; margin-top: 5px;">
                    {{ $addr->address_detail }}<br>
                    {{ $addr->district }}, {{ $addr->city }}, {{ $addr->province }}
                </p>

                <div style="display: flex; gap: 10px; margin-top: 15px;">
                    @if(!$addr->is_default)
                        <form action="{{ route('user.address.default', $addr->id) }}" method="POST">
                            @csrf
                            <button type="submit" style="background: none; border: none; color: var(--icy-blue); font-weight: 700; cursor: pointer; font-size: 0.8rem;">Jadikan Utama</button>
                        </form>
                    @endif
                    <form action="{{ route('user.address.destroy', $addr->id) }}" method="POST" onsubmit="return confirm('Hapus alamat ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="background: none; border: none; color: #ef4444; font-weight: 700; cursor: pointer; font-size: 0.8rem;">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="kiat-card" style="text-align: center;">
                <p style="font-weight: 600; color: #64748b;">Anda belum memiliki alamat tersimpan.</p>
            </div>
        @endforelse
    </div>

    {{-- FORM TAMBAH ALAMAT --}}
    <div>
        <div class="kiat-card">
            <h3 style="font-weight: 800; margin-bottom: 20px;">➕ Tambah Alamat</h3>
            <form action="{{ route('user.address.store') }}" method="POST">
                @csrf
                <input type="text" name="label" class="kiat-input" placeholder="Label (Contoh: Rumah, Kantor)" required>
                <input type="text" name="customer_name" class="kiat-input" placeholder="Nama Penerima" required>
                <input type="tel" name="customer_phone" class="kiat-input" placeholder="Nomor WhatsApp" required>
                
                {{-- Dropdown Provinsi: opsi TIDAK lagi diisi dari database. --}}
                {{-- Diisi otomatis lewat JavaScript dari API Emsifa saat halaman dimuat (lihat script di bawah). --}}
                <select name="province" id="province" class="kiat-input" required>
                    <option value="">Memuat provinsi...</option>
                </select>
                <select name="city" id="city" class="kiat-input" required disabled><option value="">Pilih Kota</option></select>
                <select name="district" id="district" class="kiat-input" required disabled><option value="">Pilih Kecamatan</option></select>

                <textarea name="address_detail" class="kiat-input" style="height: 100px;" placeholder="Detail Alamat (Nama Jalan, No. Rumah, dll)" required></textarea>
                
                <label style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; font-weight: 600; font-size: 0.9rem; cursor: pointer;">
                    <input type="checkbox" name="is_default" value="1"> Jadikan Alamat Utama
                </label>

                <button type="submit" class="btn-kiat">SIMPAN ALAMAT</button>
            </form>
        </div>
    </div>
</div>

{{-- ===================================================================== --}}
{{-- DEPENDENT DROPDOWN WILAYAH --}}
{{-- Sumber data: Public API Emsifa (https://www.emsifa.com/api-wilayah-indonesia) --}}
{{-- Alur: Provinsi -> Kota/Kabupaten -> Kecamatan. Murni Vanilla JS (Fetch API), --}}
{{-- TANPA jQuery & TANPA database lokal/seeder. --}}
{{-- ===================================================================== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Base URL API publik Emsifa.
    const BASE_API = 'https://www.emsifa.com/api-wilayah-indonesia/api';

    const provinceEl = document.getElementById('province');
    const cityEl     = document.getElementById('city');
    const districtEl = document.getElementById('district');

    // Helper: isi sebuah <select> dengan opsi hasil API.
    // PENTING: value = NAMA wilayah (inilah yang disimpan ke tabel user_addresses),
    //          data-id = ID wilayah (hanya dipakai untuk memanggil API level berikutnya).
    function fillSelect(selectEl, items, placeholder) {
        selectEl.innerHTML = `<option value="">${placeholder}</option>`;
        items.forEach(function (item) {
            const opt = document.createElement('option');
            opt.value = item.name;        // <-- yang DISUBMIT ke form adalah NAMA, bukan ID
            opt.dataset.id = item.id;     // <-- ID disimpan di data-id untuk chaining API
            opt.textContent = item.name;
            selectEl.appendChild(opt);
        });
    }

    // Helper: kosongkan & nonaktifkan dropdown turunan.
    function resetSelect(selectEl, placeholder) {
        selectEl.innerHTML = `<option value="">${placeholder}</option>`;
        selectEl.disabled = true;
    }

    // 1) SAAT HALAMAN DIMUAT: ambil daftar PROVINSI dari API Emsifa.
    fetch(`${BASE_API}/provinces.json`)
        .then(res => res.json())
        .then(data => fillSelect(provinceEl, data, 'Pilih Provinsi'))
        .catch(() => { provinceEl.innerHTML = '<option value="">Gagal memuat provinsi</option>'; });

    // 2) PEMICU API KOTA: saat PROVINSI berubah -> ambil KOTA/KABUPATEN sesuai ID provinsi.
    provinceEl.addEventListener('change', function () {
        resetSelect(cityEl, 'Memuat kota...');
        resetSelect(districtEl, 'Pilih Kecamatan');

        // Ambil ID dari opsi provinsi yang dipilih (disimpan di data-id).
        const provinceId = this.selectedOptions[0] ? this.selectedOptions[0].dataset.id : '';
        if (!provinceId) return;

        fetch(`${BASE_API}/regencies/${provinceId}.json`)
            .then(res => res.json())
            .then(data => { fillSelect(cityEl, data, 'Pilih Kota'); cityEl.disabled = false; })
            .catch(() => { cityEl.innerHTML = '<option value="">Gagal memuat kota</option>'; });
    });

    // 3) PEMICU API KECAMATAN: saat KOTA berubah -> ambil KECAMATAN sesuai ID kota.
    cityEl.addEventListener('change', function () {
        resetSelect(districtEl, 'Memuat kecamatan...');

        const cityId = this.selectedOptions[0] ? this.selectedOptions[0].dataset.id : '';
        if (!cityId) return;

        fetch(`${BASE_API}/districts/${cityId}.json`)
            .then(res => res.json())
            .then(data => { fillSelect(districtEl, data, 'Pilih Kecamatan'); districtEl.disabled = false; })
            .catch(() => { districtEl.innerHTML = '<option value="">Gagal memuat kecamatan</option>'; });
    });
});
</script>
@endsection
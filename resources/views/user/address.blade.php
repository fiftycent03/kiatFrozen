@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
    :root { --icy-blue: #0ea5e9; --frost-white: #ffffff; --deep-ocean: #0c4a6e; --ice-bg: #f0f9ff; --crystal-border: #e0f2fe; }
    body { background-color: var(--ice-bg); font-family: 'Plus Jakarta Sans', sans-serif; }
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
                
                <select name="province" id="province" class="kiat-input" required>
                    <option value="">Pilih Provinsi</option>
                    @foreach($provinces as $prov) <option value="{{ $prov }}">{{ $prov }}</option> @endforeach
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

{{-- Script Ajax Wilayah sama dengan yang ada di Cart --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#province').on('change', function() {
        let prov = $(this).val();
        $('#city').prop('disabled', true).html('<option value="">Memproses...</option>');
        if(prov) $.get("{{ url('/api/cities') }}/" + encodeURIComponent(prov), function(data) {
            let html = '<option value="">Pilih Kota</option>';
            data.forEach(item => html += `<option value="${item.city_name}">${item.city_name}</option>`);
            $('#city').prop('disabled', false).html(html);
        });
    });

    $('#city').on('change', function() {
        let city = $(this).val();
        $('#district').prop('disabled', true).html('<option value="">Memproses...</option>');
        if(city) $.get("{{ url('/api/districts') }}/" + encodeURIComponent(city), function(data) {
            let html = '<option value="">Pilih Kecamatan</option>';
            data.forEach(item => html += `<option value="${item.district_name}">${item.district_name}</option>`);
            $('#district').prop('disabled', false).html(html);
        });
    });
});
</script>
@endsection
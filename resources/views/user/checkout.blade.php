@extends('layouts.app')

@section('content')
{{-- Konten disesuaikan dengan Tema Abyss/Pearl/Gold: variabel warna lama di-remap --}}
{{-- ke palet baru (primary → lagoon, bg-body → pearl, text-main → ink). --}}
<style>
    :root {
        --primary: #16808A;                       /* lagoon — aksen teal */
        --primary-soft: rgba(22,128,138,0.12);    /* lagoon lembut */
        --text-main: #101B22;                      /* ink — teks utama */
        --bg-body: #F6F1E7;                         /* pearl — dasar halaman */
    }

    body { background-color: var(--bg-body); font-family: 'Inter', sans-serif; }

    .checkout-container { max-width: 1000px; margin: 40px auto; padding: 0 20px; display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px; }

    /* Card Styling */
    .glass-card { background: white; border-radius: 24px; padding: 28px; border: 1px solid rgba(226, 232, 240, 0.8); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.02); }
    .card-title { font-size: 1.25rem; font-weight: 700; color: var(--text-main); margin-bottom: 24px; display: flex; align-items: center; gap: 10px; }

    /* Form Styling */
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-size: 0.875rem; font-weight: 600; color: #64748b; margin-bottom: 8px; }
    .input-field { width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid #e2e8f0; background: #fcfdfe; transition: all 0.2s; outline: none; font-size: 0.95rem; }
    .input-field:focus { border-color: var(--primary); box-shadow: 0 0 0 4px var(--primary-soft); }
    
    /* Order Summary Styling */
    .product-list { margin-bottom: 20px; }
    .product-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
    .product-info h5 { font-size: 0.95rem; font-weight: 600; color: var(--text-main); margin: 0; }
    .product-info p { font-size: 0.8rem; color: #94a3b8; margin: 0; }

    .price-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 0.95rem; color: #475569; }
    .price-row.total { margin-top: 20px; padding-top: 20px; border-top: 2px dashed #e2e8f0; font-size: 1.25rem; font-weight: 800; color: var(--primary); }

    /* Shipping Badge */
    #display-shipping { background: var(--primary-soft); color: var(--primary); padding: 2px 8px; border-radius: 6px; font-size: 0.85rem; font-weight: 700; }

    /* Action Button — CTA emas (gold) dengan teks abyss sesuai tema premium */
    .btn-pay { width: 100%; padding: 16px; background: linear-gradient(135deg, #E4C24E 0%, #D4AF37 100%); color: #071726; border: none; border-radius: 16px; font-weight: 700; font-size: 1rem; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 10px 20px -5px rgba(212, 175, 55, 0.35); }
    .btn-pay:hover { transform: translateY(-2px); box-shadow: 0 15px 25px -5px rgba(212, 175, 55, 0.45); }
    .btn-pay:disabled { background: #cbd5e1; box-shadow: none; cursor: not-allowed; transform: none; }

    @media (max-width: 768px) { .checkout-container { grid-template-columns: 1fr; margin: 20px auto; } }
</style>

<div class="checkout-container">
    <!-- Left: Shipping Information -->
    <div class="glass-card">
        <h3 class="card-title">📦 Informasi Pengiriman</h3>
        <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
            @csrf
            <div class="form-group">
                <label>Nama Penerima</label>
                <input type="text" name="customer_name" class="input-field" placeholder="Masukkan nama lengkap" required>
            </div>

            <div class="form-group">
                <label>Nomor WhatsApp</label>
                <input type="tel" name="customer_phone" class="input-field" placeholder="08xxxxxx" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Provinsi</label>
                    <select name="province" id="province" class="input-field" required>
                        <option value="">Pilih Provinsi</option>
                        <option value="Jawa Timur">Jawa Timur</option>
                        <option value="Bali">Bali</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Kota</label>
                    <input type="text" name="city" id="city" class="input-field" placeholder="Mojokerto" required>
                </div>
            </div>

            <div class="form-group">
                <label>Kecamatan</label>
                <input type="text" name="district" id="district" class="input-field" placeholder="Mojoanyar" required>
            </div>

            <div class="form-group">
                <label>Alamat Lengkap</label>
                <textarea name="customer_address" class="input-field" rows="3" placeholder="Nama jalan, nomor rumah, RT/RW" required></textarea>
            </div>

            <div class="form-group">
                <label>Metode Pembayaran</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <label style="border: 1px solid #e2e8f0; padding: 15px; border-radius: 12px; cursor: pointer; display: flex; align-items: center; gap: 10px;">
                        <input type="radio" name="payment_channel" value="transfer" required checked> Transfer Bank
                    </label>
                    <label style="border: 1px solid #e2e8f0; padding: 15px; border-radius: 12px; cursor: pointer; display: flex; align-items: center; gap: 10px;">
                        <input type="radio" name="payment_channel" value="cod" required> COD
                    </label>
                </div>
            </div>
    </div>

    <!-- Right: Order Summary -->
    <div style="position: sticky; top: 20px; align-self: start;">
        <div class="glass-card">
            <h3 class="card-title">📝 Ringkasan Pesanan</h3>
            <div class="product-list">
                @foreach($cart as $item)
                <div class="product-item">
                    <div class="product-info">
                        <h5>{{ $item['name'] }}</h5>
                        <p>{{ $item['qty'] }} Unit</p>
                    </div>
                    <span style="font-weight: 700; color: var(--text-main);">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>

            <div class="price-row">
                <span>Subtotal</span>
                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="price-row">
                <span>Biaya Pengiriman</span>
                <span id="display-shipping">Rp 0</span>
            </div>

            <div class="price-row total">
                <span>Total Bayar</span>
                <span id="display-total">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>

            <button type="submit" class="btn-pay" id="btn-submit" disabled>
                Lengkapi Alamat...
            </button>
            <p style="text-align: center; font-size: 0.75rem; color: #94a3b8; margin-top: 15px;">
                🔐 Pembayaran aman & terenkripsi
            </p>
        </div>
    </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        function checkShipping() {
            let p = $('#province').val();
            let c = $('#city').val();
            let d = $('#district').val();

            if(p && c && d) {
                $('#btn-submit').text('Mengecek Ongkir...');
                $.post("{{ route('shipping.cost') }}", {
                    _token: "{{ csrf_token() }}",
                    province: p, city: c, district: d
                }, function(res) {
                    if(res.success) {
                        let sub = {{ $subtotal }};
                        let ship = res.cost;
                        $('#display-shipping').text('Rp ' + new Intl.NumberFormat('id-ID').format(ship));
                        $('#display-total').text('Rp ' + new Intl.NumberFormat('id-ID').format(sub + ship));
                        $('#btn-submit').prop('disabled', false).text('Konfirmasi & Bayar');
                    } else {
                        $('#display-shipping').text('Tidak Terjangkau');
                        $('#btn-submit').prop('disabled', true).text('Wilayah Tidak Terjangkau');
                    }
                });
            }
        }

        $('#province, #city, #district').on('change blur', checkShipping);
    });
</script>
@endsection
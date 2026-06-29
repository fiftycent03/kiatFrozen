@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
    :root { --icy-blue: #0ea5e9; --frost-white: #ffffff; --deep-ocean: #0c4a6e; --ice-bg: #f0f9ff; --crystal-border: #e0f2fe; }
    body { background-color: var(--ice-bg); font-family: 'Plus Jakarta Sans', sans-serif; color: var(--deep-ocean); }
    .container-kiat { max-width: 1200px; margin: 40px auto; padding: 0 24px; display: grid; grid-template-columns: 1fr 420px; gap: 32px; }
    
    /* Card Alamat */
    .address-section { background: white; border-radius: 24px; padding: 24px; border: 1px solid var(--crystal-border); margin-bottom: 20px; }
    .btn-change-addr { background: #f0f9ff; border: 1.5px solid var(--icy-blue); color: var(--icy-blue); padding: 8px 16px; border-radius: 12px; font-size: 0.8rem; font-weight: 700; cursor: pointer; transition: 0.3s; }
    .btn-change-addr:hover { background: var(--icy-blue); color: white; }

    /* Modal Styling */
    .kiat-modal { display: none; position: fixed; z-index: 1000; inset: 0; background: rgba(12, 74, 110, 0.4); backdrop-filter: blur(4px); padding: 20px; }
    .modal-content { background: white; width: 100%; max-width: 500px; margin: 50px auto; border-radius: 32px; padding: 30px; max-height: 80vh; overflow-y: auto; }
    .addr-item-option { border: 1.5px solid var(--ice-bg); border-radius: 20px; padding: 15px; margin-bottom: 12px; cursor: pointer; transition: 0.2s; }
    .addr-item-option:hover { border-color: var(--icy-blue); background: var(--ice-bg); }

    /* Checkout Panel */
    .checkout-panel { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-radius: 32px; padding: 30px; border: 1px solid var(--frost-white); box-shadow: 0 10px 30px rgba(0,0,0,0.05); position: sticky; top: 20px; }
    .kiat-input { width: 100%; padding: 14px; border-radius: 15px; border: 1.5px solid var(--crystal-border); background: var(--ice-bg); margin-bottom: 15px; font-weight: 600; outline: none; box-sizing: border-box; }
    .btn-checkout { width: 100%; padding: 18px; border-radius: 20px; border: none; background: var(--icy-blue); color: white; font-weight: 800; cursor: pointer; }
    .btn-checkout:disabled { background: #cbd5e1; cursor: not-allowed; }

    /* Quantity Controls */
    .qty-btn { background: var(--ice-bg); border: 1px solid var(--crystal-border); width: 32px; height: 32px; border-radius: 10px; cursor: pointer; font-weight: 800; color: var(--icy-blue); transition: 0.2s; display: flex; align-items: center; justify-content: center; }
    .qty-btn:hover:not(:disabled) { background: var(--icy-blue); color: white; }
    .qty-btn:disabled { opacity: 0.3; cursor: not-allowed; }

    /* State Kosong */
    .empty-cart-card { background: white; border-radius: 32px; padding: 60px 40px; text-align: center; border: 1px solid var(--crystal-border); grid-column: span 2; }
</style>

<div class="container-kiat">
    @if(count($cart) > 0)
        <!-- KOLOM KIRI: ALAMAT & PRODUK -->
        <div>
            <h2 style="font-weight: 800; margin-bottom: 24px;">❄️ Keranjang KIAT</h2>

            <!-- CARD ALAMAT AKTIF -->
            <div class="address-section">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <span style="font-weight: 800; font-size: 0.75rem; color: var(--icy-blue); text-transform: uppercase;">Dikirim Ke:</span>
                        <div id="display-name" style="font-weight: 800; font-size: 1.1rem; margin-top: 5px;">
                            {{ $selectedAddress->customer_name ?? 'Alamat Belum Tersedia' }}
                        </div>
                        <div id="display-detail" style="color: #64748b; font-size: 0.85rem; margin-top: 4px;">
                            @if($selectedAddress)
                                {{ $selectedAddress->customer_phone }}<br>
                                {{ $selectedAddress->address_detail ?? $selectedAddress->customer_address }}, 
                                {{ $selectedAddress->district }}, {{ $selectedAddress->city }}
                            @endif
                        </div>
                    </div>
                    <button type="button" class="btn-change-addr" onclick="openModalAlamat()">Ganti Alamat</button>
                </div>
            </div>

            <!-- DAFTAR PRODUK -->
            @foreach($cart as $id => $item)
                <div style="display:flex; align-items:center; background:white; padding:20px; border-radius:24px; margin-bottom:15px; border:1px solid var(--crystal-border);">
                    <img src="{{ asset('storage/' . $item['image']) }}" style="width:80px; height:80px; object-fit:cover; border-radius:16px;">
                    <div style="flex:1; margin-left:20px;">
                        <h4 style="margin:0; font-weight: 800;">{{ $item['name'] }}</h4>
                        <div style="font-weight: 700; color: var(--icy-blue); margin-bottom: 8px;">Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
                        
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <!-- Update Quantity Form dengan Batasan Minimal Dinamis -->
                            <div style="display: flex; align-items: center; gap: 10px; background: var(--ice-bg); padding: 5px 10px; border-radius: 12px; border: 1px solid var(--crystal-border);">
                                <form action="{{ route('cart.updateQty', $id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" value="decrease">
                                    {{-- KUNCI: Tombol DISABLED jika qty sudah mencapai min_pembelian produk --}}
                                    <button type="submit" class="qty-btn" {{ $item['qty'] <= ($item['min_pembelian'] ?? 1) ? 'disabled' : '' }}>-</button>
                                </form>
                                <span style="font-weight: 800; min-width: 25px; text-align: center;">{{ $item['qty'] }}</span>
                                <form action="{{ route('cart.updateQty', $id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" value="increase">
                                    <button type="submit" class="qty-btn">+</button>
                                </form>
                            </div>

                            <!-- Tombol Hapus -->
                            <form action="{{ route('cart.remove', $id) }}" method="POST" onsubmit="return confirm('Hapus produk ini dari keranjang?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: #ef4444; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                                    🗑️ Hapus
                                </button>
                            </form>
                        </div>
                        {{-- LABEL PERINGATAN MINIMAL BELI --}}
                        <small style="color: #ef4444; font-size: 0.75rem; font-weight: 700; display: block; margin-top: 5px;">
                            ⚠️ Minimal pembelian: {{ $item['min_pembelian'] ?? 1 }} {{ $item['satuan'] ?? 'kg' }}
                        </small>
                    </div>
                    <div style="font-weight:800; font-size: 1.1rem;">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</div>
                </div>
            @endforeach
        </div>

        <!-- KOLOM KANAN: PANEL CHECKOUT -->
        <div class="checkout-panel">
            <h3 style="font-weight: 800; margin-bottom: 20px;">🚚 Checkout</h3>
            
            <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                @csrf
                <input type="hidden" name="customer_name" id="in-name" value="{{ $selectedAddress->customer_name ?? '' }}">
                <input type="hidden" name="customer_phone" id="in-phone" value="{{ $selectedAddress->customer_phone ?? '' }}">
                <input type="hidden" name="province" id="in-prov" value="{{ $selectedAddress->province ?? '' }}">
                <input type="hidden" name="city" id="in-city" value="{{ $selectedAddress->city ?? '' }}">
                <input type="hidden" name="district" id="in-dist" value="{{ $selectedAddress->district ?? '' }}">
                <input type="hidden" name="address_detail" id="in-detail" value="{{ $selectedAddress->address_detail ?? $selectedAddress->customer_address ?? '' }}">

                <select name="shipping_service" id="shipping_service" class="kiat-input">
                    <option value="standard">📦 Layanan Standar (+Rp 0)</option>
                    <option value="express">⚡ Layanan Express (+Rp 5.000)</option>
                </select>

                <select name="payment_method" class="kiat-input" required>
                    <option value="">Pilih Pembayaran</option>
                    <option value="transfer">🏦 Transfer Bank</option>
                    <option value="cod">Bayar di Tempat (COD)</option>
                </select>

                <textarea name="notes" class="kiat-input" style="height: 60px; resize: none;" placeholder="Catatan untuk penjual..."></textarea>

                <div style="padding: 20px; background: white; border-radius: 20px; border: 1px solid var(--crystal-border);">
                    <div style="display:flex; justify-content: space-between; font-weight:600; color:#64748b;">
                        <span>Harga Barang</span><span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <div style="display:flex; justify-content: space-between; font-weight:600; color:#64748b; margin-top:8px;">
                        <span>Ongkir</span><span id="display-shipping">Rp 0</span>
                    </div>
                    <div style="margin-top:15px; border-top:2px dashed var(--crystal-border); padding-top:10px; display:flex; justify-content:space-between; font-weight:900; color:var(--icy-blue); font-size:1.4rem;">
                        <span>Total</span><span id="display-total">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <button type="submit" id="btn-submit" class="btn-checkout" disabled>PROSES PESANAN</button>
            </form>
        </div>
    @else
        <div class="empty-cart-card">
            <h2 style="font-weight: 800;">Keranjangmu Kosong 🧊</h2>
            <p style="color: #64748b; margin-bottom: 20px;">Yuk, isi dengan frozen food pilihanmu!</p>
            <a href="{{ route('produk.kategori') }}" style="background:var(--icy-blue); color:white; padding:15px 30px; border-radius:15px; text-decoration:none; font-weight:800;">Mulai Belanja</a>
        </div>
    @endif
</div>

<!-- MODAL PILIH ALAMAT -->
<div id="modalAlamat" class="kiat-modal">
    <div class="modal-content">
        <h3 style="font-weight: 800; margin-bottom: 20px;">Buku Alamat Saya</h3>
        <div id="address-list">
            @forelse($addresses as $addr)
                <div class="addr-item-option" onclick='pilihAlamat(@json($addr))'>
                    <div style="font-weight: 800;">{{ $addr->label }} — {{ $addr->customer_name }}</div>
                    <div style="font-size: 0.85rem; color:#64748b;">
                        {{ $addr->district }}, {{ $addr->city }}
                    </div>
                </div>
            @empty
                <p style="text-align: center; color: #64748b;">Belum ada alamat di profil.</p>
            @endforelse
        </div>
        <a href="{{ route('user.address.index') }}" style="display:block; text-align:center; color:var(--icy-blue); font-weight:700; margin-top:15px; text-decoration:none;">+ Kelola Alamat di Profil</a>
        <button type="button" onclick="closeModal()" style="width:100%; margin-top:15px; border:none; background:none; font-weight:700; color:#64748b; cursor:pointer;">Tutup</button>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let baseOngkir = 0;
    let subtotal = parseInt("{{ $total ?? 0 }}");

    function openModalAlamat() { $('#modalAlamat').fadeIn(); }
    function closeModal() { $('#modalAlamat').fadeOut(); }

    function pilihAlamat(addr) {
        $('#display-name').text(addr.customer_name);
        $('#display-detail').html(`${addr.customer_phone}<br>${addr.address_detail}, ${addr.district}, ${addr.city}`);
        $('#in-name').val(addr.customer_name);
        $('#in-phone').val(addr.customer_phone);
        $('#in-prov').val(addr.province);
        $('#in-city').val(addr.city);
        $('#in-dist').val(addr.district);
        $('#in-detail').val(addr.address_detail);
        fetchShippingCost(addr.province, addr.city, addr.district);
        closeModal();
    }

    function fetchShippingCost(prov, city, dist) {
        $('#btn-submit').text('MENGHITUNG...').prop('disabled', true);
        $.post("{{ route('shipping.cost') }}", {
            _token: "{{ csrf_token() }}", province: prov, city: city, district: dist
        }, function(res) {
            if(res.success) {
                baseOngkir = parseInt(res.cost);
                calculate();
                $('#btn-submit').text('PROSES PESANAN').prop('disabled', false);
            }
        });
    }

    function calculate() {
        let extra = $('#shipping_service').val() === 'express' ? 5000 : 0;
        let totalOngkir = baseOngkir + extra;
        $('#display-shipping').text('Rp ' + new Intl.NumberFormat('id-ID').format(totalOngkir));
        $('#display-total').text('Rp ' + new Intl.NumberFormat('id-ID').format(subtotal + totalOngkir));
    }

    $('#shipping_service').on('change', calculate);

    @if(isset($selectedAddress))
        $(document).ready(function() {
            fetchShippingCost("{{ $selectedAddress->province }}", "{{ $selectedAddress->city }}", "{{ $selectedAddress->district }}");
        });
    @endif
</script>
@endsection
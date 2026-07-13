@extends('layouts.app')

@section('content')
{{-- Konten disesuaikan dengan Tema Abyss/Pearl/Gold: variabel warna lama di-remap --}}
{{-- ke palet baru (icy-blue → lagoon, ice-bg → pearl, deep-ocean → ink) sehingga --}}
{{-- seluruh kartu, input, dan aksen ikut berganti tema tanpa mengubah strukturnya. --}}
<style>
    :root {
        --icy-blue: #16808A;                    /* lagoon — aksen teal */
        --frost-white: #ffffff;
        --deep-ocean: #101B22;                  /* ink — teks utama */
        --ice-bg: #F6F1E7;                       /* pearl — dasar lembut */
        --crystal-border: rgba(16,27,34,0.10);   /* border ink lembut */
    }
    body { background-color: var(--ice-bg); font-family: 'Inter', sans-serif; color: var(--deep-ocean); }
    /* CTA utama memakai aksen emas (gold) sesuai tema premium */
    .btn-checkout { background: #D4AF37 !important; color: #071726 !important; }
    .btn-checkout:disabled { background: #cbd5e1 !important; color: #ffffff !important; }
    .container-kiat { max-width: 1200px; margin: 40px auto; padding: 0 24px; display: grid; grid-template-columns: 1fr 420px; gap: 32px; }
    
    /* Card Alamat */
    .address-section { background: white; border-radius: 24px; padding: 24px; border: 1px solid var(--crystal-border); margin-bottom: 20px; }
    .btn-change-addr { background: #ffffff; border: 1.5px solid var(--icy-blue); color: var(--icy-blue); padding: 8px 16px; border-radius: 12px; font-size: 0.8rem; font-weight: 700; cursor: pointer; transition: 0.3s; }
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
                @php $minBeli = (int) ($item['min_pembelian'] ?? 1); @endphp
                <div style="display:flex; align-items:center; background:white; padding:20px; border-radius:24px; margin-bottom:15px; border:1px solid var(--crystal-border);">
                    <img src="{{ asset('storage/' . $item['image']) }}" style="width:80px; height:80px; object-fit:cover; border-radius:16px;">
                    <div style="flex:1; margin-left:20px;">
                        <h4 style="margin:0; font-weight: 800;">{{ $item['name'] }}</h4>
                        <div style="font-weight: 700; color: var(--icy-blue); margin-bottom: 8px;">Rp {{ number_format($item['price'], 0, ',', '.') }}</div>

                        <div style="display: flex; align-items: center; gap: 15px;">
                            <!-- Kontrol Quantity: tombol biasa (type=button) + AJAX, BUKAN form submit lagi -->
                            <!-- sehingga klik +/- tidak me-reload halaman. -->
                            <div style="display: flex; align-items: center; gap: 10px; background: var(--ice-bg); padding: 5px 10px; border-radius: 12px; border: 1px solid var(--crystal-border);">
                                {{-- KUNCI: type="button" + data-action; handler jQuery memanggil API & update DOM --}}
                                <button type="button" class="qty-btn qty-change" data-id="{{ $id }}" data-action="decrease"
                                        {{ (int) $item['qty'] <= $minBeli ? 'disabled' : '' }}>-</button>
                                <span class="qty-value" data-id="{{ $id }}" style="font-weight: 800; min-width: 25px; text-align: center;">{{ $item['qty'] }}</span>
                                <button type="button" class="qty-btn qty-change" data-id="{{ $id }}" data-action="increase">+</button>
                            </div>

                            <!-- Tombol Hapus (tetap form submit biasa) -->
                            <form action="{{ route('cart.remove', $id) }}" method="POST" onsubmit="return confirm('Hapus produk ini dari keranjang?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: #ef4444; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                                    🗑️ Hapus
                                </button>
                            </form>
                        </div>
                        {{-- PERINGATAN MINIMAL BELI: hanya tampil saat qty sudah di batas minimum. --}}
                        {{-- display awal dihitung server; JS akan show/hide saat qty berubah via AJAX. --}}
                        <small id="warn-{{ $id }}" style="color: #ef4444; font-size: 0.75rem; font-weight: 700; margin-top: 5px; display: {{ (int) $item['qty'] <= $minBeli ? 'block' : 'none' }};">
                            ⚠️ Sudah di batas minimal pembelian: {{ $minBeli }} {{ $item['satuan'] ?? 'kg' }}
                        </small>
                    </div>
                    <div class="row-subtotal" data-id="{{ $id }}" style="font-weight:800; font-size: 1.1rem;">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</div>
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

                {{-- Ongkir di-hardcode 0, metode pembayaran dikunci ke Midtrans — dropdown dihapus. --}}
                <input type="hidden" name="shipping_service" value="standard">
                <input type="hidden" name="payment_method" value="midtrans">

                <textarea name="notes" class="kiat-input" style="height: 60px; resize: none;" placeholder="Catatan untuk penjual..."></textarea>

                <div style="padding: 20px; background: white; border-radius: 20px; border: 1px solid var(--crystal-border);">
                    <div style="display:flex; justify-content: space-between; font-weight:600; color:#64748b;">
                        <span>Harga Barang</span><span id="display-barang">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <div style="display:flex; justify-content: space-between; font-weight:600; color:#64748b; margin-top:8px;">
                        <span>Ongkir</span><span id="display-shipping">Rp 0</span>
                    </div>
                    <div style="margin-top:15px; border-top:2px dashed var(--crystal-border); padding-top:10px; display:flex; justify-content:space-between; font-weight:900; color:var(--icy-blue); font-size:1.4rem;">
                        <span>Total</span><span id="display-total">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Tombol aktif jika sudah ada alamat terpilih (dari sesi/riwayat), dinonaktifkan untuk --}}
                {{-- guest tanpa alamat. JS akan mengaktifkannya saat user memilih alamat dari modal. --}}
                <button type="submit" id="btn-submit" class="btn-checkout"
                        {{ isset($selectedAddress) && $selectedAddress ? '' : 'disabled' }}>
                    {{ isset($selectedAddress) && $selectedAddress ? '💳 BAYAR SEKARANG' : 'PILIH ALAMAT DULU' }}
                </button>
            </form>
        </div>
    @else
        <div class="empty-cart-card">
            <h2 style="font-weight: 800;">Keranjangmu Kosong 🧊</h2>
            <p style="color: #64748b; margin-bottom: 20px;">Yuk, isi dengan frozen food pilihanmu!</p>
            {{-- CTA emas sesuai tema baru --}}
            <a href="{{ route('produk.kategori') }}" style="background:#D4AF37; color:#071726; padding:15px 30px; border-radius:15px; text-decoration:none; font-weight:800;">Mulai Belanja</a>
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
{{-- Midtrans Snap.js: library popup pembayaran. Client Key diambil dari config/services.php. --}}
<script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
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
        // Ongkir 0 untuk semua wilayah — tidak ada cek shipping_rates lagi.
        $('#display-shipping').text('Rp 0');
        $('#display-total').text('Rp ' + new Intl.NumberFormat('id-ID').format(subtotal));
        // Aktifkan tombol checkout setelah alamat dipilih.
        $('#btn-submit').prop('disabled', false).text('💳 BAYAR SEKARANG');
        closeModal();
    }

    function calculate() {
        // Ongkir selalu 0 — semua wilayah diizinkan checkout.
        $('#display-shipping').text('Rp 0');
        $('#display-total').text('Rp ' + new Intl.NumberFormat('id-ID').format(subtotal));
    }

    $(document).ready(function() {
        calculate();

        // ============================================================
        // INTERCEPT CHECKOUT FORM → MIDTRANS SNAP POPUP
        // ============================================================
        // Mencegah submit form tradisional; order dibuat via AJAX agar server
        // bisa membalas JSON berisi Snap Token, lalu popup Midtrans dibuka di sini.
        $('#checkout-form').on('submit', function(e) {
            e.preventDefault();
            const $btn  = $('#btn-submit');
            const $form = $(this);
            $btn.prop('disabled', true).text('MEMPROSES...');

            $.ajax({
                url:      $form.attr('action'),
                method:   'POST',
                data:     $form.serialize(),
                dataType: 'json',
                success: function(res) {
                    if (!res.success) {
                        alert(res.message || 'Terjadi kesalahan. Silakan coba lagi.');
                        $btn.prop('disabled', false).text('💳 BAYAR SEKARANG');
                        return;
                    }

                    if (res.snap_token && typeof window.snap !== 'undefined') {
                        // Token diterima — buka popup pembayaran Midtrans Snap.
                        // res.success_url = /order/success/{id}, res.detail_url = /user/order/{id} (Order Detail).
                        window.snap.pay(res.snap_token, {
                            onSuccess: function(result) {
                                // Pembayaran berhasil TUNTAS — konfirmasi ke backend dengan status
                                // 'paid' (mengurangi stok), lalu redirect ke halaman SUKSES.
                                // .finally() dipakai agar redirect tetap terjadi walau request
                                // konfirmasi gagal jaringan.
                                fetch("{{ route('payment.confirm') }}", {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                                    },
                                    body: JSON.stringify({ order_id: res.order_id, status: 'paid' })
                                }).finally(function() {
                                    window.location.href = res.success_url;
                                });
                            },
                            onPending: function() {
                                // Menunggu pembayaran (VA/QR belum ditransfer) — status order TETAP
                                // 'pending', tapi kirim status 'pending' ke backend supaya stok tetap
                                // dikunci untuk order ini (mencegah stok "dijual" ke pembeli lain
                                // selagi VA masih aktif ditunggu). Redirect ke halaman ORDER DETAIL
                                // (bukan success) — di sanalah instruksi VA/QR & status terkini tampil.
                                fetch("{{ route('payment.confirm') }}", {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                                    },
                                    body: JSON.stringify({ order_id: res.order_id, status: 'pending' })
                                }).finally(function() {
                                    window.location.href = res.detail_url;
                                });
                            },
                            onError: function() {
                                // Pembayaran gagal di sisi Midtrans — order tetap tersimpan (alur
                                // "Pay Later"), jadi arahkan ke Order Detail agar user bisa coba
                                // lagi lewat tombol "Lanjutkan Pembayaran" (bukan ke /cart, karena
                                // cart sudah dikosongkan sejak order dibuat).
                                alert('Pembayaran gagal. Silakan coba lagi dari halaman detail pesanan.');
                                window.location.href = res.detail_url;
                            },
                            onClose: function() {
                                // ============================================================
                                // POPUP DITUTUP TANPA BAYAR (onClose) — ALUR "PAY LATER":
                                // Order TIDAK DIHAPUS. Order sudah sah tersimpan di database
                                // (dan sudah dilengkapi snap_token dari store()), jadi user
                                // tinggal diarahkan ke halaman Order Detail — di sana tombol
                                // "Lanjutkan Pembayaran" akan membuka ULANG sesi pembayaran yang
                                // sama kapan pun dia siap membayar.
                                // ============================================================
                                window.location.href = res.detail_url;
                            }
                        });
                    } else {
                        // Midtrans belum dikonfigurasi (snap_token null) — redirect langsung.
                        window.location.href = res.success_url;
                    }
                },
                error: function(xhr) {
                    // Tampilkan pesan validasi Laravel jika ada, atau pesan generik.
                    let msg = 'Terjadi kesalahan server. Silakan coba lagi.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    alert(msg);
                    $btn.prop('disabled', false).text('💳 BAYAR SEKARANG');
                }
            });
        });
    });

    // ============================================================
    // HANDLER TOMBOL QTY (+/-) — AJAX, MENAHAN RELOAD HALAMAN
    // ============================================================
    $(document).on('click', '.qty-change', function(e) {
        e.preventDefault();
        const btn    = $(this);
        const id     = btn.data('id');
        const action = btn.data('action');

        $.post("{{ url('/cart') }}/" + id + "/qty", {
            _token: "{{ csrf_token() }}", action: action
        }, function(res) {
            if (!res.success) {
                $('.qty-change[data-id="' + id + '"][data-action="decrease"]').prop('disabled', true);
                $('#warn-' + id).show();
                return;
            }

            $('.qty-value[data-id="' + id + '"]').text(res.qty);
            $('.row-subtotal[data-id="' + id + '"]').text('Rp ' + new Intl.NumberFormat('id-ID').format(res.subtotal));
            subtotal = parseInt(res.grand_total);
            $('#display-barang').text('Rp ' + new Intl.NumberFormat('id-ID').format(subtotal));
            calculate();
            $('.qty-change[data-id="' + id + '"][data-action="decrease"]').prop('disabled', res.at_min);
            if (res.at_min) { $('#warn-' + id).show(); } else { $('#warn-' + id).hide(); }
        });
    });
</script>
@endsection
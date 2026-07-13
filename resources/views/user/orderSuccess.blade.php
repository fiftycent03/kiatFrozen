@extends('layouts.app')

@section('content')
{{-- Konten disesuaikan dengan Tema Abyss/Pearl/Gold: kartu pearl, judul ink/Fraunces, --}}
{{-- kotak total memakai dasar pearl + aksen lagoon, CTA bayar memakai gold. --}}
<div style="max-width: 600px; margin: 60px auto; background: #ffffff; padding: 40px; border-radius: 30px; text-align: center; box-shadow: 0 20px 40px -12px rgba(7,23,38,0.12); border: 1px solid rgba(16,27,34,0.06);">
    <div style="width: 84px; height: 84px; margin: 0 auto; display: grid; place-items: center; border-radius: 9999px; background: rgba(212,175,55,0.12); font-size: 42px;">✅</div>
    <h2 style="color: #101B22; font-family: 'Fraunces', serif; font-weight: 600; margin-top: 20px;">Pesanan Diterima!</h2>
    <p style="color: #64748b;">Kode Pesanan: <strong style="color:#16808A;">{{ $order->code }}</strong></p>

    <div style="background: #F6F1E7; padding: 25px; border-radius: 20px; margin: 30px 0; border: 1px solid rgba(16,27,34,0.06);">
        <p style="margin: 0; color: #16808A; font-weight: 700; font-size: 0.9rem; letter-spacing: 0.08em;">TOTAL TAGIHAN</p>
        <h1 style="margin: 5px 0; color: #101B22; font-family: 'Fraunces', serif;">Rp {{ number_format($order->total, 0, ',', '.') }}</h1>
    </div>

    @if($order->payment_channel == 'transfer')
    <div style="text-align: left; background: #F6F1E7; padding: 20px; border-radius: 15px; border: 1px solid rgba(16,27,34,0.08); margin-bottom: 25px;">
        <p style="font-weight: 700; margin-bottom: 10px; color: #101B22;">Instruksi Pembayaran:</p>
        <ol style="color: #475569; font-size: 0.9rem; padding-left: 20px;">
            <li>Transfer ke Bank BCA <strong>123-456-789</strong> a/n KIAT Frozen.</li>
            <li>Foto bukti transfer Anda.</li>
            <li>Klik tombol di bawah untuk unggah bukti bayar.</li>
        </ol>
    </div>
    @endif

    @if(!empty($order->snap_token))
    {{-- ===== PEMBAYARAN ONLINE MIDTRANS (muncul jika Snap Token berhasil dibuat) ===== --}}
    {{-- CTA bayar utama memakai aksen emas (gold) --}}
    <button id="pay-button" style="display: block; width: 100%; background: #D4AF37; color: #071726; padding: 18px; border-radius: 15px; border: none; font-weight: 800; cursor: pointer; margin-bottom: 15px;">
        💳 Bayar Online Sekarang
    </button>

    {{-- snap.js Midtrans (SANDBOX). Untuk produksi ganti ke https://app.midtrans.com/snap/snap.js --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <script>
        // Saat tombol diklik -> munculkan popup pembayaran Midtrans memakai Snap Token dari server.
        document.getElementById('pay-button').onclick = function () {
            window.snap.pay('{{ $order->snap_token }}', {
                onSuccess: function () {
                    // PENTING: harus fetch ke payment.confirm dulu (status 'paid') sebelum
                    // pindah halaman — stok baru dikurangi & cart baru dikosongkan di sana
                    // sejak logika ini dipindahkan dari checkout awal (store()).
                    fetch("{{ route('payment.confirm') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ order_id: {{ $order->id }}, status: 'paid' })
                    }).finally(function () {
                        window.location.href = "{{ route('order.show', $order->id) }}";
                    });
                },
                onPending: function () {
                    // Sama seperti onSuccess, tapi status tetap 'pending' (mis. menunggu transfer VA).
                    fetch("{{ route('payment.confirm') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ order_id: {{ $order->id }}, status: 'pending' })
                    }).finally(function () {
                        window.location.href = "{{ route('order.show', $order->id) }}";
                    });
                },
                onError: function () { alert('Pembayaran gagal, silakan coba lagi.'); }
                // Tidak ada onClose: order ini sudah ada sebelum popup dibuka (retry-bayar,
                // bukan checkout baru), jadi menutup popup di sini tidak menghapus order.
            });
        };
    </script>
    @endif

    {{-- Tombol sekunder: dasar abyss (kontras dengan CTA emas) --}}
    <a href="{{ route('order.show', $order->id) }}" style="display: block; background: #071726; color: #F6F1E7; padding: 18px; border-radius: 15px; text-decoration: none; font-weight: 700;">
        Lihat Detail
    </a>
</div>
@endsection
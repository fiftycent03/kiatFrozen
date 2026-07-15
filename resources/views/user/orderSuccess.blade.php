@extends('layouts.app')

@section('content')
{{-- Konten disesuaikan dengan Tema Abyss/Pearl/Gold: kartu pearl, judul ink/Fraunces, --}}
{{-- kotak total memakai dasar pearl + aksen lagoon, CTA bayar memakai gold. --}}
{{-- PEROMBAKAN ALUR PEMBAYARAN: integrasi Midtrans (Snap.js/popup) sudah dihapus --}}
{{-- total, diganti Transfer Bank Manual — halaman ini kini menampilkan info rekening --}}
{{-- + form upload bukti transfer langsung, tanpa payment gateway apa pun. --}}
<div style="max-width: 600px; margin: 60px auto; background: #ffffff; padding: 40px; border-radius: 30px; text-align: center; box-shadow: 0 20px 40px -12px rgba(7,23,38,0.12); border: 1px solid rgba(16,27,34,0.06);">
    <div style="width: 84px; height: 84px; margin: 0 auto; display: grid; place-items: center; border-radius: 9999px; background: rgba(212,175,55,0.12); font-size: 42px;">✅</div>
    <h2 style="color: #101B22; font-family: 'Fraunces', serif; font-weight: 600; margin-top: 20px;">Pesanan Diterima!</h2>
    <p style="color: #64748b;">Kode Pesanan: <strong style="color:#16808A;">{{ $order->code }}</strong></p>

    <div style="background: #F6F1E7; padding: 25px; border-radius: 20px; margin: 30px 0; border: 1px solid rgba(16,27,34,0.06);">
        <p style="margin: 0; color: #16808A; font-weight: 700; font-size: 0.9rem; letter-spacing: 0.08em;">TOTAL TAGIHAN</p>
        <h1 style="margin: 5px 0; color: #101B22; font-family: 'Fraunces', serif;">Rp {{ number_format($order->total, 0, ',', '.') }}</h1>
    </div>

    {{-- LOGIKA STATUS: tampilan berbeda tergantung payment_status saat ini. --}}
    @if($order->payment_status === 'paid')
        {{-- Sudah di-ACC Admin — tidak perlu upload apa pun lagi. --}}
        <div style="background: rgba(22,128,138,0.08); border: 1px solid rgba(22,128,138,0.2); padding: 20px; border-radius: 15px; margin-bottom: 25px;">
            <p style="font-weight: 700; color: #16808A; margin: 0;">✅ Pembayaran sudah dikonfirmasi Admin. Terima kasih!</p>
        </div>
    @else
        {{-- Rekening tujuan transfer — WAJIB tampil selama pesanan belum lunas,
             baik status 'pending' (belum upload), 'awaiting_verification' (sudah
             upload, menunggu Admin), maupun 'rejected' (bukti ditolak, upload ulang). --}}
        <div style="text-align: left; background: #F6F1E7; padding: 20px; border-radius: 15px; border: 1px solid rgba(16,27,34,0.08); margin-bottom: 20px;">
            <p style="font-weight: 700; margin-bottom: 10px; color: #101B22;">Instruksi Pembayaran:</p>
            <ol style="color: #475569; font-size: 0.9rem; padding-left: 20px; margin: 0;">
                <li>Transfer ke Bank BCA <strong>123-456-789</strong> a/n KIAT Frozen sejumlah tagihan di atas.</li>
                <li>Foto/screenshot bukti transfer Anda.</li>
                <li>Unggah fotonya lewat form di bawah ini.</li>
            </ol>
        </div>

        @if($order->payment_status === 'rejected')
            <div style="background: rgba(226,104,63,0.08); border: 1px solid rgba(226,104,63,0.25); padding: 15px; border-radius: 15px; margin-bottom: 20px; text-align: left;">
                <p style="font-weight: 700; color: #E2683F; margin: 0;">⚠️ Bukti transfer sebelumnya ditolak Admin. Silakan unggah ulang foto yang benar.</p>
            </div>
        @endif

        @if($order->payment_status === 'awaiting_verification')
            {{-- Sudah upload, tinggal menunggu — form TIDAK ditampilkan lagi supaya
                 tidak membingungkan user untuk upload ulang selagi masih ditinjau. --}}
            <div style="background: rgba(212,175,55,0.1); border: 1px solid rgba(212,175,55,0.3); padding: 20px; border-radius: 15px; margin-bottom: 20px;">
                <p style="font-weight: 700; color: #101B22; margin: 0;">🕐 Bukti transfer Anda sedang diverifikasi Admin. Mohon tunggu.</p>
            </div>
        @else
            {{-- FORM UPLOAD BUKTI TRANSFER: mengirim file ke OrderController@uploadProof,
                 yang menyimpan file ke storage/app/public/payment_proofs lalu mengubah
                 payment_status menjadi 'awaiting_verification'. --}}
            <form action="{{ route('order.uploadProof', $order->id) }}" method="POST" enctype="multipart/form-data" style="text-align: left; margin-bottom: 20px;">
                @csrf
                <label style="display:block; font-weight:700; font-size:0.85rem; color:#101B22; margin-bottom:8px;">Unggah Bukti Transfer</label>
                <input type="file" name="payment_proof" accept="image/*" required
                       style="width:100%; padding:10px; border:1.5px solid rgba(16,27,34,0.15); border-radius:12px; margin-bottom:12px; background:#fff;">
                <button type="submit" style="display:block; width:100%; background:#D4AF37; color:#071726; padding:16px; border-radius:15px; border:none; font-weight:800; cursor:pointer;">
                    📤 Kirim Bukti Transfer
                </button>
            </form>
        @endif
    @endif

    {{-- Tombol sekunder: dasar abyss (kontras dengan CTA emas) --}}
    <a href="{{ route('order.show', $order->id) }}" style="display: block; background: #071726; color: #F6F1E7; padding: 18px; border-radius: 15px; text-decoration: none; font-weight: 700;">
        Lihat Detail Pesanan
    </a>
</div>
@endsection

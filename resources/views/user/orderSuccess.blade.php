@extends('layouts.app')

@section('content')
<div style="max-width: 600px; margin: 60px auto; background: white; padding: 40px; border-radius: 30px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
    <div style="font-size: 60px;">❄️</div>
    <h2 style="color: #0c4a6e; font-weight: 800; margin-top: 20px;">Pesanan Diterima!</h2>
    <p style="color: #64748b;">Kode Pesanan: <strong>{{ $order->code }}</strong></p>

    <div style="background: #f0f9ff; padding: 25px; border-radius: 20px; margin: 30px 0;">
        <p style="margin: 0; color: #0ea5e9; font-weight: 700; font-size: 0.9rem;">TOTAL TAGIHAN</p>
        <h1 style="margin: 5px 0; color: #0c4a6e;">Rp {{ number_format($order->total, 0, ',', '.') }}</h1>
    </div>

    @if($order->payment_channel == 'transfer')
    <div style="text-align: left; background: #f8fafc; padding: 20px; border-radius: 15px; border: 1px solid #e2e8f0; margin-bottom: 25px;">
        <p style="font-weight: 700; margin-bottom: 10px; color: #0c4a6e;">Instruksi Pembayaran:</p>
        <ol style="color: #475569; font-size: 0.9rem; padding-left: 20px;">
            <li>Transfer ke Bank BCA <strong>123-456-789</strong> a/n KIAT Frozen.</li>
            <li>Foto bukti transfer Anda.</li>
            <li>Klik tombol di bawah untuk unggah bukti bayar.</li>
        </ol>
    </div>
    @endif

    <a href="{{ route('order.show', $order->id) }}" style="display: block; background: #0ea5e9; color: white; padding: 18px; border-radius: 15px; text-decoration: none; font-weight: 700;">
        Unggah Bukti / Lihat Detail
    </a>
</div>
@endsection
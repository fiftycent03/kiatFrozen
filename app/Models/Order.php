<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'user_id', 'payment_status', 'fulfillment_status', 'shipping_tracking_number',
        'subtotal', 'shipping_fee', 'total', 'province', 'city', 'district',
        'shipping_service', 'shipping_date', 'customer_name', 'customer_phone',
        'customer_address', 'notes', 'payment_channel', 'payment_proof', 'paid_at',
        // Kolom Proof of Delivery (diisi oleh kurir saat mengantar).
        'delivery_proof', 'delivered_at',
        // Kurir yang ditugaskan admin.
        'courier_id',
    ];

    // Cast kolom tanggal kustom ke objek Carbon agar ->format() & ->diffForHumans() bisa dipanggil.
    // Tanpa cast ini, Laravel mengembalikannya sebagai string mentah dari database.
    protected $casts = [
        'paid_at'       => 'datetime',
        'shipping_date' => 'datetime',
        'delivered_at'  => 'datetime',  // cast ini yang mencegah error "format() on string"
    ];

    public function items() { return $this->hasMany(OrderItem::class, 'order_id'); }

    public function courier() { return $this->belongsTo(User::class, 'courier_id'); }
}
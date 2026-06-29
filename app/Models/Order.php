<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'user_id', 'payment_status', 'fulfillment_status', 'shipping_tracking_number', 
        'subtotal', 'shipping_fee', 'total', 'province', 'city', 'district',
        'shipping_service', 'shipping_date', 'customer_name', 'customer_phone', 
        'customer_address', 'notes', 'payment_channel', 'payment_proof', 'paid_at'
    ];

    public function items() { return $this->hasMany(OrderItem::class, 'order_id'); }
}